<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

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
    <div style="height: 70vh; margin-top: 30vh" class="content-center">
      <div>
        <div class="text-center">
          <h4>TrackyMe</h4>
        </div>
        <div>
            <form @submit.prevent="submit">
                <h1>Login</h1>
                <div>
                    <span>Username</span>
                    <input type="text" v-model="form.email" id="query" required class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                </div>
                <div>
                    <span>Password</span>
                    <input type="password" v-model="form.password" id="query" required class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm">
                </div>
                <div class="content-center mt-3">
                  <button type="submit" class="btn btn-outline-primary">Login</button>
                </div>
            </form>
            <div class="content-center align-items-center">
              <hr class="horizontal-divisor"/>
              or 
              <hr class="horizontal-divisor"/>
            </div>
            <div class="content-center">
                <button @click="getUserAuth()" type="button" class="btn btn-outline-success">Auth</button>
            </div>
        </div>
      </div>
    </div>
</template>