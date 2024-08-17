<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import Header from '@/Components/Header.vue';

import { register } from 'swiper/element/bundle';
register();

const props = defineProps({
  user: Object,
  playlists: Object
})

const slidesPerView = ref(5);

const updateSlidesPerView = () => {
  const width = window.innerWidth;
  if (width >= 1300) {
    slidesPerView.value = 5;
  } else if (width >= 992) {
    slidesPerView.value = 4;
  } else if (width >= 768) {
    slidesPerView.value = 3;
  } else if (width >= 576) {
    slidesPerView.value = 2;
  } else {
    slidesPerView.value = 1;
  }
};


const goToPlaylist = (id) => {
  window.location = '/playlists/' + id
}

onMounted(() => {
  updateSlidesPerView(); // Set initial value
  window.addEventListener('resize', updateSlidesPerView);
});

onUnmounted(() => {
  window.removeEventListener('resize', updateSlidesPerView);
});
</script>

<template>
  <Header :user="props.user">
    <template #content>
      <div class="d-flex flex-column justify-content-center h-100 align-items-center w-100">
        <div class="d-flex flex-column h-100 w-100 ">
          <div class="d-flex justify-content-center align-items-center" style="height: 65px; margin-bottom: 5vh">
            <span class="text-center" style="font-weight: 600; font-size: 24px">
              Playlists
            </span>
            <img class="mx-2" src="../../../icons/Spotify_Logo_RGB_Black.png" height="34" ></img>
          </div>
          <div style="flex: 1;" class="d-flex justify-content-center align-items-center w-100">
              <div class="w-100 h-100">
                  <swiper-container
                    v-if="playlists.items.length > 0"
                    :slides-per-view="slidesPerView"
                    space-between="20"
                    pagination-type="fraction"
                    pagination-el=".my-swiper"
                    grid-rows="2"
                    grid-fill="row"
                    navigation="true"
                    class="h-100"
                  >
                    <swiper-slide v-for="(playlist, index) in playlists.items" :key="index">
                      <div class="d-flex flex-column justify-content-center align-items-center">
                        <div class="card" @click="goToPlaylist(playlist.id)" style="margin: 1rem; height: 15.5rem; width: 14rem; border-radius: 15px 15px 9px 9px; cursor: pointer; background-color: #575b58; margin-bottom: 0px; display: flex; flex-direction: column;">
                            <img v-if="playlist['images'] !== null" class="card-img-top" :src="playlist['images'][0]['url']" style="padding: 16px 16px 8px 16px"/>
                            <img v-else class="card-img-top" src="https://via.placeholder.com/150" />
                            <div class="d-flex h-100 align-items-center justify-content-between" style="padding-left: 16px; padding-right: 16px; padding-bottom: 8px">
                              <div>
                                <span class="badge bg-primary" style=" height: 20px; background-color: #34897A !important">{{playlist.tracks.total}} Songs</span>
                                <span class="badge bg-primary ms-1" style="max-width: 60px; height: 20px; background-color: #34897A !important">
                                  {{ playlist.public ? 'Public' : 'Private' }}
                                </span>
                              </div>
                                <span class="badge bg-primary ms-1" style="max-width: 45px; height: 20px; background-color: #34897A !important">i</span>
                            </div>
                        </div>
                        <div class="mx-3" style="display: flex; align-items: center; width: 14rem;">
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%; font-size: 0.9rem; font-weight: 600; text-align: center">{{ playlist.name }}</span>
                        </div>
                      </div>
                    </swiper-slide>
                  </swiper-container>
                  <div v-else class="d-flex justify-content-center align-items-center h-100 w-100 flex-column">
                      <img src="../../../icons/no_playlist.svg" height="340" width="340"></img>
                      <span style="color:  #292D2A;  font-weight: 600; font-size: 20px">You don't have any playlists</span>
                  </div>
                  <div class="d-flex justify-content-center align-items-center w-100">
                    <div class="my-swiper"/>
                  </div>
              </div>
          </div>
          <div class="d-flex justify-content-end mt-3 me-3" style="height: 100px;">
          </div>
        </div>
      </div>
    </template>
  </Header>
</template>

<style scoped>
</style>

<style css>
.my-swiper > span {
  font-weight: 500; font-size: 18px
}

:root {
  --swiper-navigation-color: #34897A;
  --swiper-pagination-color: #34897A;
}

</style>