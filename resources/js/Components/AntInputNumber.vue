<template>
    <a-input-number
        style="width: 100%"
        :formatter="formatter"
        v-model:value="internalValue"
        :min="0"
        @change="handleError"
    />
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue';

const props = defineProps<{
    amount: number,
}>();

const emit = defineEmits<{
    (e: 'update:amount', value: number): void;
    (e: 'clearError'): void;
}>();

const internalValue = ref(props.amount);

watch(internalValue, (newValue) => {
    emit('update:amount', newValue);
});

const formatter = (value: number) =>
    `₱ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

const handleError = () => {
   emit('clearError')
}

</script>

<style lang="scss" scoped></style>

