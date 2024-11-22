<template>
    <AuthenticatedLayout>
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
                    <a-button type="primary" @click="generate">
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

const form = ref<{ extension: string; dateRange: [Dayjs, Dayjs] }>({
    extension: "pdf",
    dateRange: [dayjs(), dayjs()],
});
const generate = () => {
    axios
        .get(route("accounting.reports.generate.special.gc.approved"), {
            params: {
                format: form.value.extension,
                date: [
                    form.value.dateRange[0].format("YYYY-MM-DD"),
                    form.value.dateRange[1].format("YYYY-MM-DD"),
                ],
            },
            responseType: "blob",
        })
        .then(async (response: AxiosResponse) => {
            // loadingProgress.value = true;

            // await waitForEvent;

            const file = new Blob([response.data], { type: "application/pdf" });
            const fileURL = URL.createObjectURL(file);
            window.open(fileURL, "_blank"); // Open the PDF in a new tab
        })
        .catch((e) => {
            let message = "please check all the fields";
            if (e.status === 404) {
                message = "there was no transaction on this selected date!";
            }
            notification.error({
                message: "Error",
                description: `Something Went wrong,  ${message}`,
            });
        });
};
</script>
