<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { h } from 'vue';
import {
    UserOutlined,
    SolutionOutlined,
    LoadingOutlined,
    SmileOutlined,
} from '@ant-design/icons-vue';
const items = [
    {
        title: 'Login',
        status: 'finish',
        icon: h(UserOutlined),
    },
    {
        title: 'Verification',
        status: 'finish',
        icon: h(SolutionOutlined),
    },
    {
        title: 'Pay',
        status: 'process',
        icon: h(LoadingOutlined),
    },
    {
        title: 'Done',
        status: 'wait',
        icon: h(SmileOutlined),
    },
];
</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-card>
                        <a-tag color="processing" class="mb-3"><InfoCircleOutlined /> Enter Barcode Here Or Scan Barcode</a-tag>
                        <a-input type="number" placeholder="Enter Barcode No" size="large" />
                    </a-card>
                </a-col>
                <a-col :span="14">
                    <a-card v-if="isFetching">
                        <a-timeline mode="right">
                            <a-timeline-item>Create a services site 2015-09-01</a-timeline-item>
                            <a-timeline-item>Solve initial network problems 2015-09-01</a-timeline-item>
                            <a-timeline-item color="red">
                                <template #dot><clock-circle-outlined style="font-size: 16px" /></template>
                                Technical testing 2015-09-01
                            </a-timeline-item>
                            <a-timeline-item>Network problems being solved 2015-09-01</a-timeline-item>
                        </a-timeline>
                    </a-card>
                </a-col>
            </a-row>
            <a-card class="mt-10" v-if="isFetching">
                <a-steps :items="items"></a-steps>
            </a-card>
        </div>
    </AuthenticatedLayout>
</template>

<script>

export default {
    data() {
        return {
            isFetching: true,
        }
    },
}
</script>
