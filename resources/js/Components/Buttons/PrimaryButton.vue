<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  icon: {
    type: String,
    default: 'none'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  noIcon: {
    type: Boolean,
    default: false
  },
  borderPrimary: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    default: 'Button'
  },
  type: {
    type: String,
    default: 'button'
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['click']);
</script>

<template>
  <div class="q-pa-sm">
    <button 
      :type="props.type" 
      class="btn" 
      :disabled="props.disabled || props.loading"
      @click="emit('click')"
      style="background-color: #34897A; color: #f7f9fb"
    >
      <div class="spinner-border spinner-border-sm" role="status" v-if="loading">
        <span class="visually-hidden">Loading...</span>
      </div>
      <span v-else>
        <span style="color: #f7f9fb; font-weight: 500" v-if="!props.loading">
          {{ props.label }}
        </span>
      </span>
    </button>
  </div>
</template>

<style scoped></style>