<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

// Состояние меню
const isOpen = ref(false);

// Настройки
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

// Флаг для предотвращения озвучивания при выключенном режиме
let isSpeechActive = false;

// Синтез речи
let synth = null;
let isSpeaking = ref(false);
let availableVoices = ref([]);
let lastFocusedElement = null;
let focusTimeout = null;
let selectionTimeout = null;
let lastSelectedText = '';

// Функция для сохранения настроек в localStorage
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
    // Главная проверка - если режим озвучки выключен, ничего не озвучиваем
    if (!settings.value.speakMode) return;
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
        utterance.onend = () => { isSpeaking.value = false; };
        utterance.onerror = () => { isSpeaking.value = false; };
        synth.speak(utterance);
    } catch (error) {
        console.error('Speech error:', error);
        isSpeaking.value = false;
    }
};

const stopSpeaking = () => {
    if (synth) { 
        synth.cancel(); 
        isSpeaking.value = false; 
    }
};

// ========== УДАЛЕНИЕ ВСЕХ СЛУШАТЕЛЕЙ ==========
const removeAllListeners = () => {
    document.body.removeEventListener('focus', handleFocus, true);
    document.removeEventListener('mouseup', handleTextSelection);
};

// ========== ДОБАВЛЕНИЕ СЛУШАТЕЛЕЙ ==========
const addAllListeners = () => {
    if (settings.value.speakOnFocus) {
        document.body.addEventListener('focus', handleFocus, true);
    }
    if (settings.value.speakSelectedText) {
        document.addEventListener('mouseup', handleTextSelection);
    }
};

// ========== ОЗВУЧИВАНИЕ ПРИ ФОКУСЕ (TAB) ==========
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
    // Двойная проверка - если режим выключен, не озвучиваем
    if (!settings.value.speakMode || !settings.value.speakOnFocus) return;
    const element = event.target;
    if (lastFocusedElement === element) return;
    lastFocusedElement = element;
    
    if (focusTimeout) clearTimeout(focusTimeout);
    focusTimeout = setTimeout(() => {
        const description = getElementDescription(element);
        if (description) speak(description, false);
    }, 50);
};

// ========== ОЗВУЧИВАНИЕ ВЫДЕЛЕННОГО ТЕКСТА ==========
const handleTextSelection = () => {
    // Двойная проверка - если режим выключен, не озвучиваем
    if (!settings.value.speakMode || !settings.value.speakSelectedText) return;
    
    if (selectionTimeout) clearTimeout(selectionTimeout);
    
    selectionTimeout = setTimeout(() => {
        const selection = window.getSelection();
        const selectedText = selection.toString().trim();
        
        if (selectedText && selectedText !== lastSelectedText && selectedText.length > 3 && selectedText.length < 300) {
            lastSelectedText = selectedText;
            if (synth?.speaking) synth.cancel();
            setTimeout(() => speak(selectedText, true), 50);
        } else if (!selectedText) {
            lastSelectedText = '';
        }
    }, 80);
};

// ========== ВИЗУАЛЬНЫЕ НАСТРОЙКИ ==========
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
    
    // Удаляем все слушатели при сбросе
    removeAllListeners();
    
    // Останавливаем речь
    stopSpeaking();
    
    applySettings();
};

const changeFontSize = (value) => {
    settings.value.fontSize = Math.min(200, Math.max(50, value));
    applySettings();
    speak(`Размер шрифта ${settings.value.fontSize} процентов`, true);
};

// ========== ОТДЕЛЬНЫЕ WATCH ДЛЯ КАЖДОГО ПОЛЯ ==========
watch(() => settings.value.fontSize, () => saveSettingsToLocalStorage());
watch(() => settings.value.lineHeight, () => saveSettingsToLocalStorage());
watch(() => settings.value.letterSpacing, () => saveSettingsToLocalStorage());
watch(() => settings.value.highContrast, () => saveSettingsToLocalStorage());
watch(() => settings.value.grayscale, () => saveSettingsToLocalStorage());
watch(() => settings.value.invertColors, () => saveSettingsToLocalStorage());

// Главный watch для speakMode - управляет включением/выключением всей озвучки
watch(() => settings.value.speakMode, (newVal) => {
    saveSettingsToLocalStorage();
    
    if (newVal) {
        // Включаем режим озвучки
        initSpeech();
        addAllListeners();
        speak('Режим озвучки включен', true);
    } else {
        // Выключаем режим озвучки - удаляем все слушатели и останавливаем речь
        removeAllListeners();
        stopSpeaking();
        // Сбрасываем последний выделенный текст
        lastSelectedText = '';
        lastFocusedElement = null;
    }
});

// Watch для speakOnFocus - добавляет/удаляет слушатель фокуса
watch(() => settings.value.speakOnFocus, (newVal) => {
    saveSettingsToLocalStorage();
    if (!settings.value.speakMode) return;
    
    if (newVal) {
        document.body.addEventListener('focus', handleFocus, true);
        speak('Озвучивание при навигации включено', true);
    } else {
        document.body.removeEventListener('focus', handleFocus, true);
        speak('Озвучивание при навигации выключено', true);
    }
});

// Watch для speakSelectedText - добавляет/удаляет слушатель выделения текста
watch(() => settings.value.speakSelectedText, (newVal) => {
    saveSettingsToLocalStorage();
    if (!settings.value.speakMode) return;
    
    if (newVal) {
        document.addEventListener('mouseup', handleTextSelection);
        speak('Озвучивание выделенного текста включено', true);
    } else {
        document.removeEventListener('mouseup', handleTextSelection);
        lastSelectedText = '';
        speak('Озвучивание выделенного текста выключено', true);
    }
});

// ========== LIFECYCLE ==========
onMounted(() => {
    initSpeech();
    
    const saved = localStorage.getItem('accessibilitySettings');
    if (saved) {
        try {
            const parsed = JSON.parse(saved);
            settings.value = { ...settings.value, ...parsed };
            applySettings();
        } catch (e) {}
    }
    
    // Применяем слушатели только если режим озвучки включен
    if (settings.value.speakMode) {
        addAllListeners();
    }
});

onUnmounted(() => {
    removeAllListeners();
    stopSpeaking();
});
</script>

<template>
    <div class="accessibility-wrapper">
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
}

.accessibility-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #284139;
    border: 2px solid #886830;
    border-radius: 40px;
    color: #F8D794;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 20px;
}

.accessibility-btn:hover {
    background: #1a2d24;
    transform: scale(1.02);
}

.accessibility-btn.active {
    background: #F8D794;
    color: #284139;
}

.btn-icon {
    font-size: 18px;
}

.btn-arrow {
    transition: transform 0.3s;
}

.btn-arrow.rotated {
    transform: rotate(180deg);
}

.speaking-indicator {
    animation: pulse 1s infinite;
    font-size: 12px;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.accessibility-panel {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 10px;
    width: 380px;
    max-width: calc(100vw - 20px);
    background: #809076;
    border: 2px solid #886830;
    border-radius: 16px;
    z-index: 1000;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #284139;
    border-bottom: 1px solid #886830;
    border-radius: 16px 16px 0 0;
}

.panel-header h3 {
    margin: 0;
    color: #F8D794;
    font-size: 20px;
}

.reset-btn {
    padding: 8px 20px;
    background: #f44336;
    border: none;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    font-size: 20px;
}

.panel-body {
    padding: 16px;
}

.main-switch {
    background: #284139;
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 16px;
}

.speech-modes {
    margin-bottom: 16px;
}

.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 10px;
}

.setting-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.setting-name {
    font-weight: 600;
    color: #F8D794;
    font-size: 18px;
}

.setting-desc {
    font-size: 14px;
    color: #e8f0ee;
}

.divider {
    height: 1px;
    background: #886830;
    margin: 16px 0;
}

.font-controls, .line-controls, .letter-controls, .color-controls {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.font-btn, .line-btn, .letter-btn, .color-btn {
    padding: 6px 12px;
    background: #284139;
    border: 1px solid #886830;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
}

.font-value {
    color: white;
    font-weight: bold;
    min-width: 50px;
    text-align: center;
    font-size: 24px;
}

.line-btn.active, .letter-btn.active, .color-btn.active {
    background: #F8D794;
    color: #284139;
}

.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
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
    background-color: #284139;
    transition: 0.3s;
    border-radius: 24px;
    border: 1px solid #886830;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 2px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #F8D794;
}

input:checked + .slider:before {
    transform: translateX(24px);
    background-color: #284139;
}

.hint {
    background: #284139;
    padding: 10px;
    border-radius: 8px;
    margin-top: 16px;
    font-size: 14px;
    color: #F8D794;
    text-align: center;
}

.hint kbd {
    background: #1a2d24;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
}

.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

@media (max-width: 768px) {
    .accessibility-panel {
        position: fixed;
        top: auto;
        bottom: 20px;
        right: 20px;
        left: 20px;
        width: auto;
    }
    
    .accessibility-btn .btn-text {
        display: none;
    }
    
    .accessibility-btn {
        padding: 10px;
    }
    
    .setting-name, .setting-desc, .reset-btn, .panel-header h3 {
        font-size: 16px;
    }
    
    .font-value {
        font-size: 20px;
    }
    
    .font-btn, .line-btn, .letter-btn, .color-btn {
        font-size: 14px;
        padding: 4px 8px;
    }
}
</style>