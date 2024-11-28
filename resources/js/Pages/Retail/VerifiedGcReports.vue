<template>
    <AuthenticatedLayout>
        <a-card title="Generate Verified GC Report">
            <a-row>
                <a-col :span="8">
                    <div>
                        <div class="flex justify-center">
                            <a-range-picker @change="getdate" />
                        </div>
                    </div>
                    <a-row>
                        <a-col :span="12">
                            <div class="flex justify-center">
                                <a-button
                                    :disabled="daterange == null"
                                    class="mt-5 mx-2"
                                    @click="generatePdf"
                                >
                                    <FilePdfOutlined /> Generate PDF
                                </a-button>
                            </div>
                        </a-col>
                        <a-col :span="12">
                            <div class="flex justify-center">
                                <a-button
                                    :disabled="daterange == null"
                                    class="mt-5 mx-3"
                                    @click="generateExcel"
                                >
                                    <OrderedListOutlined /> Generate Excel
                                </a-button>
                            </div>
                        </a-col>
                    </a-row>
                </a-col>
                <a-col :span="16">
                    <!-- Show progress when loading percentage is between 0 and 99 -->
                    <div
                        v-if="load.percentage > 0 && load.percentage < 99"
                        class="flex justify-center"
                    >
                        <a-form-item label="Fetching Data">
                            <a-progress
                                type="dashboard"
                                :percent="load.percentage"
                            />
                        </a-form-item>
                    </div>

                    <!-- Show spinner when percentage reaches 100 but PDF is not yet generated -->
                    <div
                        v-else-if="load.percentage == 100 && !generated"
                        class="flex justify-center"
                    >
                        <a-form-item label="Generating PDF Please Wait">
                            <a-spin />
                        </a-form-item>
                    </div>

                    <!-- Show confirmation when PDF is generated -->
                    <div
                        v-else-if="load.percentage == 100 && generated"
                        class="flex justify-center"
                    >
                        <a-result
                            status="success"
                            title="Successfully Generated"
                        >
                        </a-result>
                    </div>
                </a-col>
            </a-row>
        </a-card>
        <a-modal width="900px" v-model:open="open">
            <iframe :src="pdf" width="100%" height="400px"></iframe>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const page = usePage().props;
const daterange = ref(null);
const open = ref(false);
const pdf = ref(null);
const load = ref([]);
const generated = ref(null);

const getdate = (str, obj) => {
    daterange.value = obj;
};

const generatePdf = () => {
    router.get(
        route("retail.verified_gc_report.generate_pdf"),
        {
            date: daterange.value,
        },
        {
            onSuccess: (e) => {
                pdf.value = `data:application/pdf;base64,${e.props.flash.stream}`;
                open.value = true;
                generated.value = true;
            },
            preserveState: true,
        }
    );
};

const generateExcel = () => {
    window.location.href = route("retail.verified_gc_report.generate_excel", {
        date: daterange.value,
    });
};

onMounted(() => {
    window.Echo.private(
        `verified-gc-report-pdf.${page.auth.user.user_id}`
    ).listen(".verified-report-pdf", (e) => {
        load.value = e;
        console.log(e);
    });
});
</script>
