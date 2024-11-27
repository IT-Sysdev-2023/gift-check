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
                                <a-button class="mt-5 mx-2" @click="generatePdf"
                                    ><FilePdfOutlined />Generate PDF</a-button
                                >
                            </div>
                        </a-col>
                        <a-col :span="12">
                            <div class="flex justify-center">
                                <a-button class="mt-5 mx-3"
                                    ><OrderedListOutlined />Generate
                                    Excel</a-button
                                >
                            </div>
                        </a-col>
                    </a-row>
                </a-col>
                <a-col :span="16">
                    <div class="flex justify-center">
                        <a-form-item label="Generating">
                            <a-progress type="dashboard" :percent="load" />
                        </a-form-item>
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
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const daterange = ref(null);
const open = ref(false);
const pdf = ref(null);
const load = ref(0);

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
            },
            preserveState: true,
        }
    );
};
</script>
