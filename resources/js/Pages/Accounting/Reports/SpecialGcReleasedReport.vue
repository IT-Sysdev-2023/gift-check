<template>
    <AuthenticatedLayout>
        <Head :title="title" />
    <a-breadcrumb>
        <a-breadcrumb-item>
            <Link :href="route('treasury.dashboard')">Home</Link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        <!-- hello -->
    </a-breadcrumb>
        <div class="flex justify-between">
            <div>
                <a-range-picker
                    style="width: 400px"
                    v-model:value="form.dateRange"
                />
            </div>
            <div class="flex justify-between">
                <div
                    style="
                        border: 1px solid #d8d9da;
                        display: flex;
                        align-items: center;
                        padding-left: 10px;
                        border-radius: 5px;
                    "
                    class="mr-1"
                >
                    <a-radio-group v-model:value="form.extension">
                        <a-radio :value="'pdf'">
                            <a-typography-text
                                :keyboard="form.extension === 'pdf'"
                                >Generate to PDF</a-typography-text
                            >
                        </a-radio>
                        <a-radio :value="'excel'">
                            <a-typography-text
                                :keyboard="form.extension === 'excel'"
                                >Generate to Excel</a-typography-text
                            >
                        </a-radio>
                    </a-radio-group>
                </div>
                <div>
                    <a-button
                        type="primary"
                        @click="generate"
                        :loading="state.isGenerateVisible"
                    >
                        Generate
                    </a-button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs, { Dayjs } from "dayjs";
import { ref } from "vue";
import axios, { AxiosResponse } from "axios";
import { notification } from "ant-design-vue";
import { useQueueState } from "@/stores/queue-state";
import { route } from 'ziggy-js';

defineProps<{
    title: string
}>();
const form = ref<{ extension: string; dateRange: [Dayjs, Dayjs] }>({
    extension: "pdf",
    dateRange: [dayjs(), dayjs()],
});
const state = useQueueState();

const generate = () => {
     axios
        .get(route("accounting.reports.generate.special.gc.released"), {
            params: {
                format: form.value.extension,
                date: [
                    form.value.dateRange[0].format("YYYY-MM-DD"),
                    form.value.dateRange[1].format("YYYY-MM-DD"),
                ],
            },
        }).then((e) => {
            state.setGenerateButton(true);
            state.setFloatButton(true);

            state.setOpenFloat(true);
        })
        .catch((e) => {
            let message = "please check all the fields";
            if (e.status === 404) {
                message = e.response.data.error;
            }
            notification.error({
                message: "Opps Something Went wrong",
                description: `${message}`,
            });
        });
};
</script>
