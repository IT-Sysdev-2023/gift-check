<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    columns: Array
})
</script>
<template>
    <AuthenticatedLayout>
        <a-card>
            <div class="flex justify-end mb-5">
                <a-input-search enter-button allow-clear placeholder="Input search here..." class="w-1/4" />
            </div>
            <a-table :columns="props.columns"></a-table>
        </a-card>
    </AuthenticatedLayout>
</template>
