<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import PrimaryButton from '../Components/Buttons/PrimaryButton.vue';


const form = useForm({
    password: '',
    password_confirmation: '',
})

const submit = () => {
  form.post('/password-setup', {
    onFinish: () => {
      form.reset('password_confirmation');
    }
  });
};
</script>


<template>
    <div style="height: 100%" class="d-flex justify-content-center align-items-center">
        <div style="background-color: #575b58;border-radius: 22px; padding: 1rem; box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19); height: 460px; width: 350px" class="d-flex justify-content-center align-items-center">
            <form @submit.prevent="submit" style="height: 100%; padding-left: 1rem; padding-right: 1rem" class="d-flex flex-column justify-content-center align-items-center">
              <div class="d-flex flex-column justify-content-between align-items-between" style="height: 90%">
                <div class="text-center d-flex flex-column justify-content-center align-items-center" style="height: 80px">
                  <!-- <h4>TrackyMe</h4> -->
                  <span style="color:  #f7f9fb;  font-weight: 500; font-size: 32px">Password Setup</span>
                </div>
                <div style="height: 100px" class="d-flex flex-column justify-content-between align-items-between">
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
                  <div>
                      <input 
                        type="password" 
                        v-model="form.password_confirmation"
                        id="query" 
                        required 
                        class="form-control"
                        placeholder="Repeat Password" 
                        aria-label="Small" 
                        aria-describedby="inputGroup-sizing-sm"
                        style="font-weight: 500; color:#34897A; border-radius: 7px; border: solid 3px"
                      >
                  </div>
                </div>
                <PrimaryButton
                    label="Save"
                    type="submit"
                    style="margin-top: 3rem;"
                    :disabled="form.password == '' || form.password_confirmation == ''"
                    class="full-width"
                    >
                </PrimaryButton>
              </div>
            </form>
          </div>
    </div>
</template>

<style css>
.full-width > .btn {
    width: 100%;
  }
</style>