<template>
    <AuthenticatedLayout>
        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <store-gc-request-card :countGcRequest/>
            </a-col>
            <a-col :span="8">

            </a-col>
            <a-col :span="8">

            </a-col>
        </a-row>
    </AuthenticatedLayout>
</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default {
    props: {
        countGcRequest:Object
    }
}
</script>
