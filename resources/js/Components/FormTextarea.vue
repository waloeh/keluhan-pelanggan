<!-- components/FormTextarea.vue -->
<template>
  <div class="mb-3">
    <label :for="id" class="form-label">{{ label }}</label>
    <textarea
      class="form-control"
      :id="id"
      :placeholder="placeholder"
      :rows="rows"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>
    <small v-if="error" class="text-danger">{{ error }}</small>
  </div>
</template>

<script setup>
  defineProps({
    id: String,
    label: String,
    placeholder: { type: String, default: '' },
    rows: { type: Number, default: 4 },
    modelValue: [String, Number],
    error: { type: String, default: '' }
  })

  defineEmits(['update:modelValue'])
</script>
