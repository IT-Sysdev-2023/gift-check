<template>
    <AuthenticatedLayout>
        <a-row>
            <a-col :span="8">
                <e-card title="End Of Day">
                    <a-button @click="() => $inertia.get(route('eod.list'))" type="primary" size="large" block
                        class="mb-3">
                        <LogoutOutlined /> End of Day Lists
                    </a-button>
                    <a-button @click="() => $inertia.get(route('eod.verified.gc'))" block type="primary" size="large"
                        class="mb-3">
                        <LogoutOutlined /> Store Eod Lists
                    </a-button>
                </e-card>
            </a-col>
            <a-col :span="8">

            </a-col>
            <a-col :span="8">

            </a-col>
        </a-row>
    </AuthenticatedLayout>

</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


</script>
