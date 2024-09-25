<script setup>
import { ref, onMounted, watch } from 'vue';
import Header from '@/Components/Header.vue';
import PrimaryButton from '../Components/Buttons/PrimaryButton.vue';
const props = defineProps({
  user: Object,
})
</script>

<template>
  <Header :user="props.user">
    <template #content>
      <div class="row restyle-container" style="height: 70%">
        <div class="col restyle-container">
          <div class="d-flex flex-column justify-content-start align-items-start restyle-title-container" style="height: 100%; padding-left: 3rem">
            <div class="d-flex flex-column">
              <span style="color:  #292D2A;  font-weight: 500; font-size: 48px; text-align: left; width: 90%; margin-top: 3rem; margin-bottom: 3rem" class="responsive-title restyle-title">
                Personalized Playlists at Your Fingertips
              </span>
              <span style="color: #292D2A; font-size: 18px; font-weight: 400; width: 90%" class="restyle-title-content">
                Use the recommendation builder to effortlessly discover new music tailored to your tastes. Whether you're looking for fresh tracks or hidden gems, this tool makes it easy to find and enjoy songs that resonate with you.
              </span>
            </div>
              <a href="/recommender">
                <PrimaryButton
                  label="Start By Searching"
                  type="submit"
                  style="margin-top: 3rem"
                />
              </a>
          </div>
        </div>
        <div class="col-6 d-flex justify-content-center align-items-center hide-on-mobile">
          <img src="../../icons/undraw_video_upload_3d4u.svg" style="max-height: 480px"></img>
        </div>
      </div>
      <div class="row hide-on-mobile" style="height: 30%">
        <div class="col-8 row">
          <div class="col-4 d-flex flex-column responsive-padding-blocks" style="padding-left: 3rem; padding-right: 3rem">
            <span style="font-weight: 500; font-size: 20px" class="mb-3">
              Check your playlists for inspiration 
            </span>
            <span style="font-size: 14px">
              Dive into your existing playlists to get inspired and use your favorite songs as reference for your new playlist.
            </span>
          </div>
          <div class="col-4 d-flex flex-column responsive-padding-blocks" style="padding-left: 3rem; padding-right: 3rem">
            <span style="font-weight: 500; font-size: 20px" class="mb-3">
              Search for songs to use as a reference            
            </span>
            <span style="font-size: 14px">
              Looking for that perfect track? Search for the exact song/artist using our search tool and feed the recommendation builder however you want.
            </span>
          </div>
          <div class="col-4 d-flex flex-column responsive-padding-blocks" style="padding-left: 3rem; padding-right: 3rem">
            <span style="font-weight: 500; font-size: 20px" class="mb-3">
              Create a playlist with the results
            </span>
            <span style="font-size: 14px">
              Turn the search results into your newest favorite playlist, with a few clicks the playlist will be available directly on Spotify.
            </span>
          </div>
        </div>
        <div class="col-4 d-flex justify-content-center align-items-center h-100">
          <img src="../../icons/builder_arrow.svg" style="max-width: 100%; max-height: 100%; height: auto; width: auto;"></img>
        </div>
      </div>
    </template>
  </Header>
</template>

<style scoped>
@media (max-width: 1440px) {
  .responsive-title {
      font-size: 36px !important; /* Adjust this value as needed */
  }

  .responsive-padding-blocks {
    padding-left: 2rem !important;
    padding-right: 2rem !important;
  }
}
@media (max-width: 1280px) {
  .responsive-padding-blocks {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
}

@media (max-width: 970px) {
  .hide-on-mobile {
    display: none !important;
  }

  .restyle-title {
    font-size: 26px !important;
    text-align: center !important;
    width: 100% !important;
    margin-top: 0px !important;
  }

  .restyle-title-content {
    font-size: 16px !important;
    text-align: center !important;
    width: 100% !important;
  }

  .restyle-title-container {
    padding-left: 0px !important;
    align-items: center !important;
    height: auto !important;
  }

  .restyle-container {
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: center !important;
  }
}
</style>