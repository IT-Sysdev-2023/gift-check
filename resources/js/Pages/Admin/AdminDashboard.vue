<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { h } from "vue";
import {
    UserOutlined,
    SolutionOutlined,
    LoadingOutlined,
    SmileOutlined,
} from "@ant-design/icons-vue";

const props = defineProps<{
    data: any;
    latestStatus: number
}>();

const items = props.data;

interface FormState {
    barcode: number;
}

const form: FormState = useForm({
    barcode: null,
});
const viewStatus = async () => {
    form.get(route("admin.dashboard"));
};

</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div>
            <a-row :gutter="[16, 16]">
                <a-col :span="10">
                    <a-card>
                        <a-tag color="processing" class="mb-3"
                            ><InfoCircleOutlined /> Enter Barcode Here Or Scan
                            Barcode</a-tag
                        >
                        <a-input
                            type="number"
                            placeholder="Enter Barcode No"
                            size="large"
                            v-model:value="form.barcode"
                        />
                        <a-button @click="viewStatus">View Status</a-button>
                    </a-card>
                </a-col>
                <a-col :span="14">
                    <a-card v-if="isFetching">
                        <a-timeline mode="right">
                            <a-timeline-item
                                >Create a services site
                                2015-09-01</a-timeline-item
                            >
                            <a-timeline-item
                                >Solve initial network problems
                                2015-09-01</a-timeline-item
                            >
                            <a-timeline-item color="red">
                                <template #dot
                                    ><clock-circle-outlined
                                        style="font-size: 16px"
                                /></template>
                                Technical testing 2015-09-01
                            </a-timeline-item>
                            <a-timeline-item
                                >Network problems being solved
                                2015-09-01</a-timeline-item
                            >
                        </a-timeline>
                    </a-card>
                </a-col>
            </a-row>
            <a-card class="mt-10" v-if="isFetching">
                <a-steps  :current="items.length-1" :items="items"></a-steps>
            </a-card>

        </div>
    </AuthenticatedLayout>
</template>

<script lang="ts">
export default {
    data() {
        return {
            isFetching: true,
        };
    },
};
</script>
