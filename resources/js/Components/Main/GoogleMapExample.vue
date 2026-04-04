<script setup>
import { GoogleMap, Marker, InfoWindow } from "vue3-google-map";
import { ref } from "vue";
import predrinimatel from "../../../images/Предприниматель.jpg";

const api = "AIzaSyC6L3uzc8_Ug01uxrFdYhN_Eo5mIwyaPZY";

const center = { lat: 55.160278, lng: 61.402457 };

const projects = ref([
  {
    id: 1,
    name: "ЖК Сити",
    desс: "Новый жилой комплекс рядом с вашим домом",
    lat: 55.160278,
    lng: 61.402457,
    image: predrinimatel
  },
  {
    id: 2,
    name: "Бизнес центр",
    desс: "Новый жилой комплекс рядом с вашим домом",
    lat: 55.170278,
    lng: 61.412457,
    image: predrinimatel
  }
]);

const selectedProject = ref(null);

const selectProject = (project) => {
  if (selectedProject.value?.id === project.id) {
    selectedProject.value = null;
    setTimeout(() => {
      selectedProject.value = project;
    }, 0);
  } else {
    selectedProject.value = project;
  }
};

const goToProject = (id) => {
  router.visit(`/projects/${id}`);
};

const closeMarker = () =>{
  selectProject.value = null;
}

const mainColor = "#C9D8EF";
const footerColor = "#A9BCDB";
</script>

<template>
    <div :class="`mx-auto py-6 px-4 sm:px-10 lg:px-16 bg-[${mainColor}] bg-[#C9D8EF]`">
      <GoogleMap
        :api-key="api"
        class="w-full h-[1000px] "
        :center="center"
        :zoom="15"
        @click="closeMarker"
      >
        <Marker
          v-for="project in projects"
          :key="project.id"
          :options="{ position: { lat: project.lat, lng: project.lng } }"
          @click="selectProject(project)"
        />

        <InfoWindow
          v-if="selectedProject"
          :options="{ position: { lat: selectedProject.lat, lng: selectedProject.lng } }"
        >
          <div
            class="w-52 bg-gray-200 overflow-hidden cursor-pointer"
            @click="goToProject(selectedProject.id)"
          >
            <img
              :src="selectedProject.image"
              class="w-full h-32 object-cover"
            />

            <div class="p-3">
              <h3 class="text-lg font-semibold text-gray-800">
                {{ selectedProject.name }}
              </h3>
              <p class="text-sm font-semibold  mt-4">
                {{ selectedProject.desс }}
              </p>
            </div>
          </div>
        </InfoWindow>

      </GoogleMap>
    </div>
</template>