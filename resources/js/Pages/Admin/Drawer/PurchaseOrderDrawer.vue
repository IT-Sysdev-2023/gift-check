<template>
    <a-drawer :width="500" title="Edit Form Order Purchase Details" :placement="'top'" @close="onClose">
        <template #extra>
            <a-button style="margin-right: 8px" @click="onClose">Cancel</a-button>
            <a-button type="primary" @click="onClose">Submit</a-button>
        </template>
    </a-drawer>
</template>
<script setup>
const emit = defineEmits(['close-drawer']);

const onClose = () => {
    emit('close-drawer')
}
</script>
