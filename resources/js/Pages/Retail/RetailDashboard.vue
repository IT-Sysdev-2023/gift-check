<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <store-gc-request-card :counts="countGcRequest" />
            </a-col>
            <a-col :span="8">
                <available-gc :availableGc />
            </a-col>
            <a-col :span="8">
                <sold-gc />
            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>

<script>
import AvailableGc from './Cards/AvailableGc.vue';

export default {
    props: {
        countGcRequest: Object,
        availableGc: Object
    }
}
</script>
