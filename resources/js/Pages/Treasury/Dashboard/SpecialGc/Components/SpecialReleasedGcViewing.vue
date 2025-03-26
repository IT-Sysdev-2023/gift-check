<template>
    <AuthenticatedLayout>

        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('treasury.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>
                <Link :href="route('treasury.special.gc.specialReleasedGc')">Released Special External Gc</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <a-tabs v-model:activeKey="activeKey">
                <a-tab-pane key="1" :tab="title"><a-row :gutter="[32, 16]" class="mt-5">
                        <a-col :span="12">
                            <a-descriptions size="small" title="Requested Details" layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" :span="3" label="Requisition No">{{
                                    record.data.spexgc_num
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Date Requested">{{
                                    record.data.spexgc_datereq
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Requested By">{{
                                    record.data.user
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Date Validity">{{
                                    record.data.spexgc_dateneed
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Remarks">{{
                                    record.data.approvedRequest?.reqap_remarks
                                }}</a-descriptions-item>
                                <a-descriptions-item label="Payment Type" :span="2">{{
                                    record.data.paymentTypeFormat
                                    }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions size="small" title="Approved Details" class="mt-10" layout="horizontal"
                                bordered>
                                <a-descriptions-item style="width: 50%;" :span="3" label="Date Approved">
                                    {{ record.data.spexgc_datereq }}
                                </a-descriptions-item>
                                <a-descriptions-item :span="3" label="Documents">
                                    <ant-image-preview :images="record.data.approvedRequest?.reqap_doc" />
                                </a-descriptions-item>
                                <a-descriptions-item :span="3" label="Checked By">{{
                                    record.data.approvedRequest?.reqap_checkedby
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Prepared By">{{
                                    record.data.approvedRequest?.user.full_name
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Remarks">{{
                                    record.data.spexgc_remarks
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Approved By">{{
                                    record.data.approvedRequest
                                        ?.reqap_approvedby
                                }}</a-descriptions-item>
                            </a-descriptions>
                        </a-col>
                        <a-col :span="12">
                            <a-descriptions size="small" title="Reviewed Details" layout="horizontal" bordered>
                                <a-descriptions-item style="width: 50%;" :span="3" label="Date Reviewed">
                                    {{ dayjs(reviewed.reqap_date) }}
                                </a-descriptions-item>
                                <a-descriptions-item :span="3" label="Remarks">{{
                                    reviewed.reqap_remarks
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Reviewed By">{{
                                    reviewed.user?.full_name
                                }}</a-descriptions-item>
                            </a-descriptions>
                            <a-descriptions title="Released Details" size="small" class="mt-10" layout="horizontal"
                                bordered>
                                <a-descriptions-item style="width: 50%;" :span="3" label="Date Released">
                                    {{ dayjs(released.reqap_date) }}
                                </a-descriptions-item>
                                <a-descriptions-item :span="3" label="Remarks">{{
                                    released.reqap_remarks
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Received By">{{
                                    record.data.spexgc_receviedby
                                }}</a-descriptions-item>
                                <a-descriptions-item :span="3" label="Released By">{{
                                    released.user.full_name
                                }}</a-descriptions-item>
                            </a-descriptions>
                        </a-col>
                    </a-row>
                </a-tab-pane>
                <a-tab-pane key="2" tab="Barcodes" force-render>
                    <a-table bordered :data-source="barcodes.data" :columns="[{
                        title: 'Barcode',
                        dataIndex: 'spexgcemp_barcode'
                    },
                    {
                        title: 'Denomination',
                        dataIndex: 'spexgcemp_denom'
                    },
                    {
                        title: 'Lastname',
                        dataIndex: 'spexgcemp_lname'
                    },
                    {
                        title: 'Firstname',
                        dataIndex: 'spexgcemp_fname'
                    },
                    {
                        title: 'Middlename',
                        dataIndex: 'spexgcemp_mname'
                    },
                    {
                        title: 'Name Ext.',
                        dataIndex: 'spexgcemp_extname'
                    }]">

                    </a-table>
                </a-tab-pane>
            </a-tabs>
            <div class="mt-5">
                <div class="flex gap-5">
                    <a-button>
                        <FilePdfOutlined />Reprint
                    </a-button>
                    <a-button :loading="isloading" type="primary" @click="soaPrint">
                        <FilePdfOutlined />Print SOA
                    </a-button>
                </div>
            </div>
        </a-card>
        <a-modal :open="isOpen" width="1000px" height="600">
            <iframe :src="pdf" frameborder="0" width="100%" height="600"></iframe>
            <template #footer>
                <a-button key="back" @click="handleCancel">Return</a-button>
            </template>
        </a-modal>

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
import { route } from 'ziggy-js';

const isOpen = ref(false);
const isloading = ref(false)
const props = defineProps<{
    title?: string;
    record: any;
    reviewed: any;
    released: any;
    barcodes: any;
    id: string
}>();
const activeKey = ref("1");
const page = usePage<PageWithSharedProps>().props;
const currentDate = dayjs().format("MMM DD, YYYY");
const disabledDate = (current: Dayjs) => {
    // Can not select days before today and today
    return current && current < dayjs().startOf("day");
};

const stream = ref(null);
const openIframe = ref(false);
const { openLeftNotification } = onProgress();

const pdf = ref(null)

const handleCancel = () => {
    isOpen.value = false
}




const soaPrint = () => {
    isloading.value = true
    router.get(route('treasury.special.gc.soaPrint'), {
        data: props.record,
        barcode: props.barcodes,
        released: props.released,
        id: props.id
    }, {
        onSuccess: (r: any) => {
            pdf.value = `data:application/pdf;base64,${r.props.flash.stream}`
            isOpen.value = true
            isloading.value = false
        },
        preserveState: true
    });
};
</script>
