<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link :href="route('treasury.special.gc.specialReleasedGc')"
                    >Released Special External Gc</Link
                >
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <a-row>
                <a-col :span="12">
                    <a-descriptions :title="title" layout="vertical" bordered>
                        <a-descriptions-item label="Date Requested"
                            >{{ record.data.spexgc_datereq }}</a-descriptions-item
                        >
                        <a-descriptions-item label="Requested By"
                            >{{record.data.user}}</a-descriptions-item
                        >
                        <a-descriptions-item label="Date Validity"
                            >{{ record.data.spexgc_dateneed }}</a-descriptions-item
                        >
                        <a-descriptions-item label="Remarks"
                            >{{ record.data.approvedRequest?.reqap_remarks }}</a-descriptions-item
                        >
                        <a-descriptions-item label="Payment Type" :span="2"
                            >{{ record.data.spexgc_paymentype }}</a-descriptions-item
                        >
                    </a-descriptions>
                    <a-descriptions :title="title" layout="vertical" bordered>
                        <a-descriptions-item label="Date Approved">
                            <a-badge status="processing" text="Running" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Documents"
                            >$80.00</a-descriptions-item
                        >
                        <a-descriptions-item label="Checked By"
                            >$20.00</a-descriptions-item
                        >
                        <a-descriptions-item label="Prepared By"
                            >$60.00</a-descriptions-item
                        >
                        <a-descriptions-item label="Remarks"
                            >$60.00</a-descriptions-item
                        >
                        <a-descriptions-item label="Approved By"
                            >$60.00</a-descriptions-item
                        >
                    </a-descriptions>
                </a-col>
                <a-col :span="12">
                    <a-descriptions :title="title" layout="vertical" bordered>
                        <a-descriptions-item label="Date Reviewed">
                            <a-badge status="processing" text="Running" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Remarks"
                            >$80.00</a-descriptions-item
                        >
                        <a-descriptions-item label="Reviewed By"
                            >$20.00</a-descriptions-item
                        >
                    </a-descriptions>
                    <a-descriptions :title="title" layout="vertical" bordered>
                        <a-descriptions-item label="Date Released">
                            <a-badge status="processing" text="Running" />
                        </a-descriptions-item>
                        <a-descriptions-item label="Remarks"
                            >$80.00</a-descriptions-item
                        >
                        <a-descriptions-item label="Received By"
                            >$20.00</a-descriptions-item
                        >
                        <a-descriptions-item label="Released By"
                            >$20.00</a-descriptions-item
                        >
                    </a-descriptions>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import type { UploadChangeParam } from "ant-design-vue";
import { ref } from "vue";
import dayjs, { Dayjs } from "dayjs";
import { router, useForm, usePage } from "@inertiajs/vue3";
import type { UploadFile } from "ant-design-vue";
import { PageWithSharedProps } from "@/types/index";
import { onProgress } from "@/Mixin/UiUtilities";

const props = defineProps<{
    title?: string;
    record: any;
}>();

const page = usePage<PageWithSharedProps>().props;
const currentDate = dayjs().format("MMM DD, YYYY");
const disabledDate = (current: Dayjs) => {
    // Can not select days before today and today
    return current && current < dayjs().startOf("day");
};

const stream = ref(null);
const openIframe = ref(false);
const { openLeftNotification } = onProgress();
</script>
