<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '../Components/Buttons/PrimaryButton.vue';

const form = useForm({
    email: '',
    password: '',
})

const submit = () => {
  form.post('/login', {
    onFinish: () => {
      form.reset('password');
    }
  });
};

const getUserAuth = async () => {
  await axios
    .get('/spotify/login')
    .then((response) => {
      window.location = response.data;
    })
    .catch((error) => {
      console.error('There was an error fetching the results:', error);
    });
}
</script>


<template>
    <div style="height: 100vh" class="content-center d-flex align-items-center">
      <div class="h-100 col d-flex flex-column justify-content-center align-items-center back-image-clear">
        <div style="position: absolute;top: 0;margin-top: 40px;">
          <span style="color: #292D2A;  font-weight: 500; font-size: 48px">TrackyMe</span>
        </div>
        <div style="background-color: #575b58;border-radius: 22px; padding: 1rem; box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19); height: 460px; width: 350px" class="d-flex justify-content-center align-items-center">
          <form @submit.prevent="submit" style="height: 100%; padding-left: 1rem; padding-right: 1rem" class="d-flex flex-column justify-content-center align-items-center">
            <div class="d-flex flex-column justify-content-between align-items-between" style="height: 90%">
              <div class="text-center d-flex flex-column justify-content-center align-items-center" style="height: 80px">
                <!-- <h4>TrackyMe</h4> -->
                <span style="color:  #f7f9fb;  font-weight: 500; font-size: 32px">Login Now</span>
                <span style="color:  #959595;  font-weight: 500; font-size: 14px">To create an account, use the Spotify login</span>
              </div>
              <div style="height: 100px" class="d-flex flex-column justify-content-between align-items-between">
                <div>
                    <input 
                      type="text" 
                      v-model="form.email" 
                      id="query" 
                      required 
                      placeholder="Username"
                      class="form-control" 
                      aria-label="Small" 
                      aria-describedby="inputGroup-sizing-sm"
                      style="font-weight: 500; color:#34897A; border-radius: 7px; border: solid 3px"
                    >
                </div>
                <div>
                    <input 
                      type="password" 
                      v-model="form.password" 
                      id="query" 
                      required 
                      class="form-control"
                      placeholder="Password" 
                      aria-label="Small" 
                      aria-describedby="inputGroup-sizing-sm"
                      style="font-weight: 500; color:#34897A; border-radius: 7px; border: solid 3px"
                    >
                </div>
              </div>
              <div class="d-flex flex-column justify-content-between align-items-between" style="height: 131px">
                <div class="content-center">
                  <PrimaryButton
                    label="Login"
                    type="submit"
                    style="width: 100%"
                    class="full-width"
                  />
                </div>
                <div class="content-center align-items-center">
                  <hr class="horizontal-divisor"/>
                  or 
                  <hr class="horizontal-divisor"/>
                </div>
                <div class="content-center">
                    <button @click="getUserAuth()" type="button" class="btn d-flex align-items-center w-100 justify-content-center" style="background-color: #1db954">
                      <span style="color:  #f7f9fb;  font-weight: 500; font-size: 16px">
                        Sign in with
                      </span>
                      <img class="mx-2" src="../../icons/Spotify_Logo_RGB_White.png" height="34" ></img>
                    </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="h-100 col d-flex flex-column justify-content-center align-items-center hide-on-mobile back-image">
        <span style="color:  #f7f9fb; font-weight: 500; font-size: 30px; margin-bottom: 3rem; text-align: center">
          Your Next Favorite Songs, Curated for You
        </span>
        <img src="../../icons/undraw_recording_re_5xyq.svg" height="340" width="340"></img>
        <span style="color:  #f7f9fb;  font-weight: 500; font-size: 18px; margin-bottom: 3rem; width: 70%; margin-top: 3rem;  text-align: center">
          Connect with Spotify and experience a new way of enjoying music.
        </span>
      </div>
    </div>
</template>

<style scoped>
@media (max-width: 970px) {
  .hide-on-mobile {
    display: none !important;
  }
}

.back-image {
  background: url('../../icons/tile_background.png') repeat 0 0;
}

.back-image-clear {
  background: url('../../icons/tile_background_kinda_clear.png') repeat 0 0;
  background-position: right 253px center;
}

</style>

<style css>

.full-width > .btn {
  width: 100%;
}
</style>