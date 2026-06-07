<script setup>
import { ref, onMounted, onUnmounted, onBeforeUnmount, watch, nextTick } from 'vue';

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    brandDark: 'var(--color-brand-dark)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    page: 'var(--color-page)',
    surface: 'var(--color-surface)',
    light: 'var(--color-light)',
    white: 'var(--color-white)',
    white80: 'rgba(255, 255, 255, 0.8)',
};

const fonts = {
    heading: 'var(--font-heading)',
    body: 'var(--font-body)',
};

const transitions = {
    fast: 'var(--transition-fast)',
    normal: 'var(--transition-normal)',
    slow: 'var(--transition-slow)',
};

const shadows = {
    sm: 'var(--shadow-sm)',
    md: 'var(--shadow-md)',
    lg: 'var(--shadow-lg)',
    xl: 'var(--shadow-xl)',
};

const radius = {
    sm: 'var(--radius-sm)',
    md: 'var(--radius-md)',
    lg: 'var(--radius-lg)',
    xl: 'var(--radius-xl)',
    '2xl': 'var(--radius-2xl)',
};

const isOpen = ref(false);

const settings = ref({
    fontSize: 100,
    lineHeight: 1.5,
    letterSpacing: 0,
    highContrast: false,
    grayscale: false,
    invertColors: false,
    speakMode: false,
    speakOnFocus: true,
    speakSelectedText: true
});

const isInitialized = ref(false);

let synth = null;
let isSpeaking = ref(false);
let availableVoices = ref([]);
let lastFocusedElement = null;
let focusTimeout = null;
let selectionTimeout = null;
let lastSelectedText = '';

const wrapperRef = ref(null);

const saveSettingsToLocalStorage = () => {
    localStorage.setItem('accessibilitySettings', JSON.stringify(settings.value));
};

const initSpeech = () => {
    if (typeof window !== 'undefined' && window.speechSynthesis) {
        synth = window.speechSynthesis;
        const loadVoices = () => { availableVoices.value = synth.getVoices(); };
        if (synth.getVoices().length > 0) loadVoices();
        synth.addEventListener('voiceschanged', loadVoices);
        return true;
    }
    return false;
};

const getVoice = () => {
    const voices = availableVoices.value;
    if (voices.length === 0) return null;
    let voice = voices.find(v => v.lang === 'ru-RU');
    if (!voice) voice = voices.find(v => v.lang.startsWith('ru'));
    return voice || voices[0];
};

const speak = (text, priority = false) => {
    if (!settings.value.speakMode) {
        stopSpeaking();
        return;
    }
    if (!synth && !initSpeech()) return;
    if (!text || text.length === 0) return;
    
    const processedText = text.length > 300 ? text.substring(0, 300) + ' и далее' : text;
    
    try {
        if (priority && synth.speaking) synth.cancel();
        if (synth.speaking && !priority) return;
        
        const utterance = new SpeechSynthesisUtterance(processedText);
        utterance.lang = 'ru-RU';
        utterance.rate = 0.9;
        utterance.pitch = 1;
        utterance.volume = 1;
        
        const voice = getVoice();
        if (voice) utterance.voice = voice;
        
        isSpeaking.value = true;
        utterance.onend = () => { 
            isSpeaking.value = false;
            if (!settings.value.speakMode) {
                synth.cancel();
            }
        };
        utterance.onerror = () => { 
            isSpeaking.value = false;
            synth.cancel();
        };
        synth.speak(utterance);
    } catch (error) {
        console.error('Speech error:', error);
        isSpeaking.value = false;
        stopSpeaking();
    }
};

const stopSpeaking = () => {
    if (synth) { 
        try {
            synth.cancel();
            if (synth.speaking) {
                synth.pause();
                synth.cancel();
            }
        } catch (error) {
            console.error('Error stopping speech:', error);
        }
        isSpeaking.value = false; 
    }
};

const removeAllListeners = () => {
    try {
        document.body.removeEventListener('focus', handleFocus, true);
        document.removeEventListener('mouseup', handleTextSelection);
    } catch (error) {
        console.error('Error removing listeners:', error);
    }
};

const addAllListeners = () => {
    removeAllListeners();
    
    try {
        if (settings.value.speakOnFocus) {
            document.body.addEventListener('focus', handleFocus, true);
        }
        if (settings.value.speakSelectedText) {
            document.addEventListener('mouseup', handleTextSelection);
        }
    } catch (error) {
        console.error('Error adding listeners:', error);
    }
};

const getElementDescription = (element) => {
    let text = element.getAttribute('aria-label') || 
            element.getAttribute('alt') || 
            element.getAttribute('title') ||
            element.getAttribute('placeholder');
    
    if (!text && element.innerText && element.innerText.trim().length < 100) {
        text = element.innerText.trim();
    }
    
    if (text && text.length > 1 && text.length < 150) return text;
    return null;
};

const handleFocus = (event) => {
    if (!settings.value.speakMode || !settings.value.speakOnFocus) return;
    const element = event.target;
    if (lastFocusedElement === element) return;
    lastFocusedElement = element;
    
    if (focusTimeout) clearTimeout(focusTimeout);
    stopSpeaking();
    
    focusTimeout = setTimeout(() => {
        if (!settings.value.speakMode || !settings.value.speakOnFocus) return;
        const description = getElementDescription(element);
        if (description) speak(description, true);
    }, 150);
};

const handleTextSelection = () => {
    if (!settings.value.speakMode || !settings.value.speakSelectedText) {
        stopSpeaking();
        return;
    }
    
    if (selectionTimeout) clearTimeout(selectionTimeout);
    
    selectionTimeout = setTimeout(() => {
        if (!settings.value.speakMode || !settings.value.speakSelectedText) {
            stopSpeaking();
            return;
        }
        
        const selection = window.getSelection();
        const selectedText = selection.toString().trim();
        
        if (selectedText && selectedText !== lastSelectedText && selectedText.length > 3 && selectedText.length < 300) {
            lastSelectedText = selectedText;
            stopSpeaking();
            setTimeout(() => {
                if (settings.value.speakMode && settings.value.speakSelectedText) {
                    speak(selectedText, true);
                }
            }, 50);
        } else if (!selectedText) {
            lastSelectedText = '';
        }
    }, 80);
};

const applySettings = () => {
    const root = document.documentElement;
    root.style.fontSize = `${settings.value.fontSize}%`;
    root.style.lineHeight = settings.value.lineHeight;
    root.style.letterSpacing = `${settings.value.letterSpacing}px`;
    
    if (settings.value.highContrast) {
        root.style.filter = 'contrast(1.8) brightness(0.95)';
    } else if (settings.value.grayscale) {
        root.style.filter = 'grayscale(100%)';
    } else if (settings.value.invertColors) {
        root.style.filter = 'invert(100%) hue-rotate(180deg)';
    } else {
        root.style.filter = 'none';
    }
    
    saveSettingsToLocalStorage();
};

const resetSettings = () => {
    stopSpeaking();
    
    if (focusTimeout) {
        clearTimeout(focusTimeout);
        focusTimeout = null;
    }
    if (selectionTimeout) {
        clearTimeout(selectionTimeout);
        selectionTimeout = null;
    }
    
    settings.value = {
        fontSize: 100,
        lineHeight: 1.5,
        letterSpacing: 0,
        highContrast: false,
        grayscale: false,
        invertColors: false,
        speakMode: false,
        speakOnFocus: true,
        speakSelectedText: true
    };
    
    removeAllListeners();
    lastSelectedText = '';
    lastFocusedElement = null;
    
    applySettings();
    
    setTimeout(() => stopSpeaking(), 50);
};

const changeFontSize = (value) => {
    settings.value.fontSize = Math.min(200, Math.max(50, value));
    applySettings();
    if (settings.value.speakMode) {
        speak(`Размер шрифта ${settings.value.fontSize} процентов`, true);
    }
};

watch(() => settings.value.fontSize, () => {
    if (isInitialized.value) saveSettingsToLocalStorage();
});
watch(() => settings.value.lineHeight, () => {
    if (isInitialized.value) saveSettingsToLocalStorage();
});
watch(() => settings.value.letterSpacing, () => {
    if (isInitialized.value) saveSettingsToLocalStorage();
});
watch(() => settings.value.highContrast, () => {
    if (isInitialized.value) saveSettingsToLocalStorage();
});
watch(() => settings.value.grayscale, () => {
    if (isInitialized.value) saveSettingsToLocalStorage();
});
watch(() => settings.value.invertColors, () => {
    if (isInitialized.value) saveSettingsToLocalStorage();
});

watch(() => settings.value.speakMode, (newVal, oldVal) => {
    
    if (isInitialized.value) {
        saveSettingsToLocalStorage();
    }
    
    if (newVal === true) {
        stopSpeaking();
        initSpeech();
        addAllListeners();
        
        if (isInitialized.value && oldVal === false) {
            speak('Режим озвучки включен', true);
        }
    } else if (newVal === false) {
        stopSpeaking();
        
        if (focusTimeout) {
            clearTimeout(focusTimeout);
            focusTimeout = null;
        }
        if (selectionTimeout) {
            clearTimeout(selectionTimeout);
            selectionTimeout = null;
        }
        
        removeAllListeners();
        lastSelectedText = '';
        lastFocusedElement = null;
        
        setTimeout(() => {
            if (!settings.value.speakMode) {
                stopSpeaking();
            }
        }, 100);
    }
});

watch(() => settings.value.speakOnFocus, (newVal) => {
    if (isInitialized.value) saveSettingsToLocalStorage();
    if (!settings.value.speakMode) return;
    
    if (newVal) {
        document.body.addEventListener('focus', handleFocus, true);
        if (isInitialized.value) speak('Озвучивание при навигации включено', true);
    } else {
        document.body.removeEventListener('focus', handleFocus, true);
        if (isInitialized.value) speak('Озвучивание при навигации выключено', true);
    }
});

watch(() => settings.value.speakSelectedText, (newVal) => {
    if (isInitialized.value) saveSettingsToLocalStorage();
    if (!settings.value.speakMode) return;
    
    if (newVal) {
        document.addEventListener('mouseup', handleTextSelection);
        if (isInitialized.value) speak('Озвучивание выделенного текста включено', true);
    } else {
        document.removeEventListener('mouseup', handleTextSelection);
        lastSelectedText = '';
        if (isInitialized.value) speak('Озвучивание выделенного текста выключено', true);
    }
});

const cleanup = () => {
    stopSpeaking();
    if (focusTimeout) {
        clearTimeout(focusTimeout);
        focusTimeout = null;
    }
    if (selectionTimeout) {
        clearTimeout(selectionTimeout);
        selectionTimeout = null;
    }
    removeAllListeners();
    lastSelectedText = '';
    lastFocusedElement = null;
};

if (typeof window !== 'undefined') {
    window.__accessibilityCleanup = cleanup;
}

onMounted(() => {
    cleanup();
    initSpeech();
    
    const saved = localStorage.getItem('accessibilitySettings');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            
            Object.keys(parsed).forEach(key => {
                settings.value[key] = parsed[key];
            });
            
            
            applySettings();
            
        } catch (e) {
            console.error('Error loading settings:', e);
        }
    }
    
    nextTick(() => {
        isInitialized.value = true;
        
        if (settings.value.speakMode) {
            addAllListeners();
        }
    });
    
    const handleInertiaStart = () => {
        stopSpeaking();
        if (focusTimeout) clearTimeout(focusTimeout);
        if (selectionTimeout) clearTimeout(selectionTimeout);
        lastSelectedText = '';
        lastFocusedElement = null;
    };
    
    const handleVisibilityChange = () => {
        if (document.hidden) {
            stopSpeaking();
            if (focusTimeout) clearTimeout(focusTimeout);
            if (selectionTimeout) clearTimeout(selectionTimeout);
        }
    };
    
    const handleBeforeUnload = () => {
        cleanup();
    };
    
    window.addEventListener('beforeunload', handleBeforeUnload);
    document.addEventListener('visibilitychange', handleVisibilityChange);
    document.addEventListener('inertia:start', handleInertiaStart);
    
    window.__accessibilityBeforeUnload = handleBeforeUnload;
    window.__accessibilityVisibilityChange = handleVisibilityChange;
    window.__accessibilityInertiaStart = handleInertiaStart;
});

onBeforeUnmount(() => {
    stopSpeaking();
    if (focusTimeout) clearTimeout(focusTimeout);
    if (selectionTimeout) clearTimeout(selectionTimeout);
});

onUnmounted(() => {
    if (window.__accessibilityBeforeUnload) {
        window.removeEventListener('beforeunload', window.__accessibilityBeforeUnload);
        delete window.__accessibilityBeforeUnload;
    }
    if (window.__accessibilityVisibilityChange) {
        document.removeEventListener('visibilitychange', window.__accessibilityVisibilityChange);
        delete window.__accessibilityVisibilityChange;
    }
    if (window.__accessibilityInertiaStart) {
        document.removeEventListener('inertia:start', window.__accessibilityInertiaStart);
        delete window.__accessibilityInertiaStart;
    }
    
    cleanup();
    
    if (window.__accessibilityCleanup) {
        delete window.__accessibilityCleanup;
    }
});
</script>

<template>
    <div ref="wrapperRef" class="accessibility-wrapper">
        <button 
            @click="isOpen = !isOpen"
            class="accessibility-btn"
            :class="{ 'active': isOpen }"
            :aria-expanded="isOpen"
        >
            <span class="btn-text">Доступность</span>
            <svg class="btn-arrow" :class="{ 'rotated': isOpen }" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        </button>

        <Transition name="dropdown">
            <div v-if="isOpen" class="accessibility-panel">
                <div class="panel-header">
                    <h3>Доступность</h3>
                    <button @click="resetSettings" class="reset-btn">Сбросить</button>
                </div>

                <div class="panel-body">
                    <div class="setting-item main-switch">
                        <div class="setting-info">
                            <span class="setting-name">Режим озвучки</span>
                            <span class="setting-desc">Включить голосовое сопровождение</span>
                        </div>
                        <label class="switch">
                            <input type="checkbox" v-model="settings.speakMode">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div v-if="settings.speakMode" class="speech-modes">
                        <div class="setting-item">
                            <div class="setting-info">
                                <span class="setting-name">Навигация (Tab)</span>
                                <span class="setting-desc">Озвучивать элементы при фокусе</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" v-model="settings.speakOnFocus">
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="setting-item">
                            <div class="setting-info">
                                <span class="setting-name">Выделение текста</span>
                                <span class="setting-desc">Озвучивать выделенный мышкой текст</span>
                            </div>
                            <label class="switch">
                                <input type="checkbox" v-model="settings.speakSelectedText">
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <span class="setting-name">Размер шрифта</span>
                        </div>
                        <div class="font-controls">
                            <button @click="changeFontSize(settings.fontSize - 10)" class="font-btn" :disabled="settings.fontSize <= 50">A⁻</button>
                            <span class="font-value">{{ settings.fontSize }}%</span>
                            <button @click="changeFontSize(settings.fontSize + 10)" class="font-btn" :disabled="settings.fontSize >= 200">A⁺</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <span class="setting-name">Межстрочный интервал</span>
                        </div>
                        <div class="line-controls">
                            <button v-for="val in [1, 1.3, 1.5, 1.8, 2]" :key="val" @click="settings.lineHeight = val; applySettings()" class="line-btn" :class="{ active: settings.lineHeight === val }">{{ val }}</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <span class="setting-name">Межбуквенное расстояние</span>
                        </div>
                        <div class="letter-controls">
                            <button v-for="val in [0, 0.5, 1, 1.5, 2]" :key="val" @click="settings.letterSpacing = val; applySettings()" class="letter-btn" :class="{ active: settings.letterSpacing === val }">{{ val }}px</button>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <span class="setting-name">Цветовые режимы</span>
                        </div>
                        <div class="color-controls">
                            <button @click="settings.highContrast = !settings.highContrast; settings.grayscale = false; settings.invertColors = false; applySettings()" class="color-btn" :class="{ active: settings.highContrast }">Высокий контраст</button>
                            <button @click="settings.grayscale = !settings.grayscale; settings.highContrast = false; settings.invertColors = false; applySettings()" class="color-btn" :class="{ active: settings.grayscale }">Ч/Б</button>
                            <button @click="settings.invertColors = !settings.invertColors; settings.highContrast = false; settings.grayscale = false; applySettings()" class="color-btn" :class="{ active: settings.invertColors }">Инверсия</button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.accessibility-wrapper {
    position: relative;
    display: inline-block;
    font-size: 1rem;
}

.accessibility-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--color-brand);
    border: 2px solid var(--color-white);
    border-radius: 40px;
    color: var(--color-white);
    cursor: pointer;
    transition: all var(--transition-slow);
    font-size: 0.875rem;
}

.accessibility-btn:hover {
    background: var(--color-light);
    color: var(--color-accent);
    transform: scale(1.02);
}

.accessibility-btn.active {
    background: var(--color-accent);
    color: var(--color-brand);
}

.btn-arrow {
    transition: transform var(--transition-slow);
    width: 0.875rem;
    height: 0.875rem;
}

.btn-arrow.rotated {
    transform: rotate(180deg);
}

.speaking-indicator {
    animation: pulse 1s infinite;
    font-size: 0.75rem;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.accessibility-panel {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 0.625rem;
    width: 22rem;
    max-width: calc(100vw - 1.25rem);
    background: var(--color-white);
    border: 2px solid var(--color-accent);
    border-radius: var(--radius-xl);
    z-index: 1000;
    box-shadow: var(--shadow-lg);
    font-size: 1rem;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: var(--color-brand);
    border-bottom: 2px solid var(--color-accent);
    border-radius: var(--radius-xl) var(--radius-xl) 0 0;
}

.panel-header h3 {
    margin: 0;
    color: var(--color-white);
    font-size: 1.25rem;
    font-family: var(--font-heading);
}

.reset-btn {
    padding: 0.375rem 0.875rem;
    background: var(--color-page);
    border: 1px solid var(--color-accent);
    border-radius: var(--radius-md);
    color: var(--color-brand);
    cursor: pointer;
    font-size: 1rem;
    transition: all var(--transition-fast);
}

.reset-btn:hover {
    background: var(--color-accent);
    color: var(--color-white);
}

.panel-body {
    padding: 1rem;
}

.main-switch {
    background: var(--color-light);
    padding: 0.75rem;
    border-radius: var(--radius-lg);
    margin-bottom: 1rem;
}

.speech-modes {
    margin-bottom: 1rem;
}

.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.setting-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.setting-name {
    font-weight: 600;
    color: var(--color-brand);
    font-size: 1.25rem;
    font-family: var(--font-heading);
}

.setting-desc {
    font-size: 1rem;
    color: var(--color-hover);
}

.divider {
    height: 1px;
    background: var(--color-light);
    margin: 1rem 0;
}

.font-controls, .line-controls, .letter-controls, .color-controls {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.font-btn, .line-btn, .letter-btn, .color-btn {
    padding: 0.375rem 0.75rem;
    background: var(--color-brand);
    border: 1px solid var(--color-hover);
    border-radius: var(--radius-md);
    color: var(--color-white);
    cursor: pointer;
    font-size: 1rem;
    transition: all var(--transition-fast);
}

.font-btn:hover, .line-btn:hover, .letter-btn:hover, .color-btn:hover {
    background: var(--color-hover);
}

.font-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.font-value {
    color: var(--color-brand);
    font-weight: bold;
    min-width: 3rem;
    text-align: center;
    font-size: 1.5rem;
}

.line-btn.active, .letter-btn.active, .color-btn.active {
    background: var(--color-accent);
    color: var(--color-brand);
    border-color: var(--color-accent);
}

.switch {
    position: relative;
    display: inline-block;
    width: 2.75rem;
    height: 1.375rem;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--color-light);
    transition: var(--transition-fast);
    border-radius: 1.375rem;
    border: 1px solid var(--color-hover);
}

.slider:before {
    position: absolute;
    content: "";
    height: 1rem;
    width: 1rem;
    left: 0.125rem;
    bottom: 0.125rem;
    background-color: var(--color-brand);
    transition: var(--transition-fast);
    border-radius: 50%;
}

input:checked + .slider {
    background-color: var(--color-accent);
    border-color: var(--color-accent);
}

input:checked + .slider:before {
    transform: translateX(1.375rem);
    background-color: var(--color-white);
}

.dropdown-enter-active,
.dropdown-leave-active {
    transition: all var(--transition-normal);
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-0.5rem);
}

@media (max-width: 48rem) {
    .accessibility-panel {
        position: fixed;
        top: auto;
        bottom: 1.25rem;
        right: 1.25rem;
        left: 1.25rem;
        width: auto;
    }
    
    .accessibility-btn .btn-text {
        display: none;
    }
    
    .accessibility-btn {
        padding: 0.625rem;
    }
}
</style>