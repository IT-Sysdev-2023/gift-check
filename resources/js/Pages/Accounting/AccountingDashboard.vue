<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <special-external-gc-request-card :count="count" title="Special External Gc Request" />
            </a-col>
            <a-col :span="8">
                <special-dti-gc-request-card :count="count" title="Special Dti Gc Request" />
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script setup>
const props = defineProps({
    count: Object
})
</script>
