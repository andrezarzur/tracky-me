<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';

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
    <div style="height: 100%" class="d-flex justify-content-center">
        <div>
            <form @submit.prevent="submit" style="width: 100%">
                <h1>Password Setup</h1>
                <div>
                    <input type="password" v-model="form.password" id="query" required>
                </div>
                <div>
                    <input type="password" v-model="form.password_confirmation" id="query" required>
                </div>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>
</template>