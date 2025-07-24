<template>
    <div class="mb-3">
        <label :for="id" class="form-label">{{ label }}</label>
        <input 
            class="form-control"
            :id="id"
            :placeholder="placeholder"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        >
        <small v-if="error" class="text-danger">{{ error }}</small>
    </div>
</template>
<script setup>
    defineProps({
    id: String,
    label: String,
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    modelValue: [String, Number],
    error: { type: String, default: '' }
    })

    defineEmits(['update:modelValue'])
</script>
