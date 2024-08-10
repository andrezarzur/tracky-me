<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import axios from 'axios';
import Header from '@/Components/Header.vue';
import WaveSurfer from 'wavesurfer.js'

const props = defineProps({
  user: Object,
  playlists: Object
})

const paginatedPlaylists = ref(props.playlists.items.slice(0, 12));
const currentPage = ref(1);
const itemsPerPage = ref(12);
const totalPages = computed(() => Math.ceil(props.playlists.items.length / itemsPerPage.value));


const goToPlaylist = (id) => {
  window.location = '/playlists/' + id
}

const updatePaginatedPlaylists = () => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  paginatedPlaylists.value = props.playlists.items.slice(start, end);
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
    updatePaginatedPlaylists();
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
    updatePaginatedPlaylists();
  }
};

const goToPage = (page) => {
  currentPage.value = page;
  updatePaginatedPlaylists();
};


</script>

<template>
  <Header :user="props.user">
    <template #content>
        <div class="d-flex justify-content-center">
            <div>
                <div class="d-flex justify-content-center align-items-center">
                  <h1 class="text-center">Playlists</h1>
                  <img class="mx-2" src="../../../icons/Spotify_Logo_RGB_Black.png" height="34" ></img>
                </div>
                <div class="justify-content-center d-flex">
                    <div class="d-flex flex-wrap" style="height: 100%; width: 80vw;">
                        <div v-for="(playlist, index) in paginatedPlaylists" :key="index" class="">
                            <div>
                                <div class="card" @click="goToPlaylist(playlist.id)" style="margin: 1rem; height: 15.5rem; width: 14rem; border-radius: 15px 15px 9px 9px; cursor: pointer; background-color: #575b58; margin-bottom: 0px; display: flex; flex-direction: column;">
                                    <img v-if="playlist['images'] !== null" class="card-img-top" :src="playlist['images'][0]['url']" style="padding: 16px 16px 8px 16px"/>
                                    <img v-else class="card-img-top" src="https://via.placeholder.com/150" />
                                    <div class="d-flex h-100 align-items-center justify-content-between" style="padding-left: 16px; padding-right: 16px; padding-bottom: 8px">
                                      <div>
                                        <span class="badge bg-primary" style=" height: 20px; background-color: #34897A !important">{{playlist.tracks.total}} Songs</span>
                                        <span class="badge bg-primary ms-1" style="max-width: 60px; height: 20px; background-color: #34897A !important">
                                          <!-- <img src="../../../icons/incognito.svg" ></img> -->
                                          {{ playlist.public ? 'Public' : 'Private' }}
                                        </span>
                                      </div>
                                        <span class="badge bg-primary ms-1" style="max-width: 45px; height: 20px; background-color: #34897A !important">i</span>
                                    </div>
                                </div>
                                <div class="h-100 mx-3" style="display: flex; align-items: center; height: 100%; width: 14rem;">
                                    <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; font-size: 0.9rem; font-weight: 600; text-align: center">{{ playlist.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <nav class="d-flex justify-content-end mt-3 me-3">
                    <ul class="pagination">
                      <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <a class="page-link" href="#" @click.prevent="prevPage">Previous</a>
                      </li>
                      <li
                        v-for="page in totalPages"
                        :key="page"
                        class="page-item"
                        :class="{ active: currentPage === page }"
                      >
                        <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
                      </li>
                      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <a class="page-link" href="#" @click.prevent="nextPage">Next</a>
                      </li>
                    </ul>
                </nav>
            </div>
        </div>
    </template>
  </Header>
</template>