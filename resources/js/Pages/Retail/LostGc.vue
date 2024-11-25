<template>
    <AuthenticatedLayout>
        <a-card title="Report Lost GC">
            <a-row :gutter="[16, 16]">
                <a-col :span="8">
                    <a-form-item label="Lost GC #">
                        <a-input v-model:value="data.lostGCnumber" readonly />
                    </a-form-item>
                    <a-form-item label="Date Reported">
                        <a-input v-model:value="data.dateReported" readonly />
                    </a-form-item>
                    <a-form-item label="Date Lost">
                        <a-date-picker style="width: 300px" @change="onDateLostChange" value-format="YYYY-MM-DD" />
                    </a-form-item>
                    <a-form-item label="Owner" has-feedback :help="data.errors.owner"
                        :validate-status="data.errors.owner ? 'error' : ''">
                        <a-input v-model:value="data.owner" @change="data.errors.owner = ''" />
                    </a-form-item>
                    <a-form-item label="Address" has-feedback :help="data.errors.address"
                        :validate-status="data.errors.address ? 'error' : ''">
                        <a-textarea v-model:value="data.address" @change="data.errors.address = ''" />
                    </a-form-item>
                    <a-form-item label="Contact #" has-feedback :help="data.errors.contact"
                        :validate-status="data.errors.contact ? 'error' : ''">
                        <a-input v-model:value="data.contact" @change="data.errors.contact = ''" />
                    </a-form-item>
                    <a-form-item label="Prepared By">
                        <a-input v-model:value="$page.props.auth.user.full_name" readonly />
                    </a-form-item>
                </a-col>
                <a-col :span="8">
                    <a-form-item label="Lost Barcode" has-feedback :help="data.errors.lostbarcode" :validate-status="data.errors.lostbarcode ? 'error' : ''
                        ">
                        <a-input v-model:value="data.lostbarcode" @change="data.errors.lostbarcode = ''" />
                    </a-form-item>
                    <a-form-item label="Remarks" :help="data.errors.remarks"
                        :validate-status="data.errors.remarks ? 'error' : ''">
                        <a-textarea v-model:value="data.remarks" @change="data.errors.remarks = ''" />
                    </a-form-item>
                    <div>
                        <div class="flex justify-end">
                            <a-button @click="onsubmit" type="primary">Submit</a-button>
                        </div>
                    </div>
                </a-col>
                <a-col :span="8">
                    <div>
                        <div class="flex justify-end">
                            <a-form-item label="Search Barcode">
                                <a-input-search v-model:value="barcode" placeholder="" style="width: 200px"
                                    @change="onSearch" />
                            </a-form-item>
                        </div>
                    </div>
                    <a-spin tip="Searching Barcode..." :spinning="isloading">
                        <a-card size="small" title="Lost Barcode List">
                            <a-table bordered size="small" :dataSource="barcodes" :columns="columns">
                                <template v-slot:bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'view'">
                                        <a-button type="dashedh  hh">
                                            <EyeOutlined />
                                        </a-button>
                                    </template> 
                                </template>
                            </a-table>
                        </a-card>
                    </a-spin>
                </a-col>
            </a-row>
        </a-card>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/core";
import dayjs from "dayjs";
import { useForm } from "laravel-precognition-vue-inertia";
import { ref } from "vue";
import { notification } from "ant-design-vue";

const props = defineProps({
    lostGCnumber: Number,
    barcodes: Object,
});

const isloading = ref(false);

const data = useForm("post", route("retail.submit-lost-gc"), {
    lostGCnumber: props.lostGCnumber,
    dateReported: dayjs().format("YYYY-MM-DD"),
    dateLost: dayjs().format("YYYY-MM-DD"),
    owner: null,
    address: null,
    contact: null,
    remarks: null,
    lostbarcode: null,
});

const onSearch = () => {
    router.get(
        route("retail.lostGc", {
            q: barcode.value,
        }),
        {
            onStart: () => {
                isloading.value = true;
            },
            onSuccess: () => {
                isloading.value = false;
            },
            onError: () => {
                isloading.value = false;
            },
        },
        {
            preserveState: true,
        }
    );
};
const onsubmit = () => {
    data.submit({
        onSuccess: (e) => {
            notification[e.props.flash.type]({
                message: e.props.flash.msg,
                description: e.props.flash.description,
            });
        },
    });
};
const onDateLostChange = (date) => {
    data.dateLost = dayjs(date).format("YYYY-MM-DD");
};

const columns = [
    {
        title: "Barcode",
        dataIndex: "lostgcb_barcode",
        align: "center",
        width: "33%",
    },
    {
        title: "Denomination",
        dataIndex: "lostgcb_denom",
        align: "center",
        width: "33%",
    },
    {
        title: "View",
        dataIndex: "view",
        align: "center",
        width: "33%",
    },
];
</script>
