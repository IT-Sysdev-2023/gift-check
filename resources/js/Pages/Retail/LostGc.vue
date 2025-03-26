<template>
    <AuthenticatedLayout>
        <a-card title="Report Lost GC" >
            <a-row :gutter="[16, 16]" justify="start">
                <a-col :span="10">
                    <a-form layout="horizontal">
                    <a-form-item label="Lost GC #" labelAlign="right">
                        <a-input v-model:value="data.lostGCnumber" readonly style="width: 100%" />
                    </a-form-item>
                    <a-form-item label="Date Reported" labelAlign="right">
                        <a-input v-model:value="data.dateReported" readonly  style="width: 100%"/>
                    </a-form-item>
                    <a-form-item label="Date Lost" labelAlign="right">
                        <a-date-picker style="width: 100%" @change="onDateLostChange" value-format="YYYY-MM-DD" />
                    </a-form-item>
                    <a-form-item  label="Owner" labelAlign="right"  has-feedback :help="data.errors.owner"
                        :validate-status="data.errors.owner ? 'error' : '' " >
                        <a-input v-model:value="data.owner" @change="data.errors.owner = ''"  />
                    </a-form-item>
                    <a-form-item label="Address" labelAlign="right" has-feedback :help="data.errors.address"
                        :validate-status="data.errors.address ? 'error' : ''">
                        <a-textarea v-model:value="data.address" @change="data.errors.address = ''" style="width: 100%" />
                    </a-form-item>
                    <a-form-item label="Contact #" labelAlign="right" has-feedback :help="data.errors.contact"
                        :validate-status="data.errors.contact ? 'error' : ''">
                        <a-input v-model:value="data.contact" @change="data.errors.contact = ''" style="width: 100%" />
                    </a-form-item>
                   
                    <a-form-item label="Lost Barcode" labelAlign="right" has-feedback :help="data.errors.lostbarcode" :validate-status="data.errors.lostbarcode ? 'error' : ''
                        " >
                        <a-input v-model:value="data.lostbarcode" @change="data.errors.lostbarcode = ''" style="width: 100%" />
                    </a-form-item>
                    <a-form-item label="Remarks"  labelAlign="right" :help="data.errors.remarks"
                        :validate-status="data.errors.remarks ? 'error' : ''">
                        <a-textarea v-model:value="data.remarks" @change="data.errors.remarks = ''" style="width: 100%" />
                    </a-form-item>
                    <a-form-item label="Prepared By">
                        <a-input v-model:value="$page.props.auth.user.full_name" readonly style="width: 100%"/>
                    </a-form-item>
    
                        <div class="flex justify-end mt-4">
                            <a-button @click="onsubmit" type="primary" style="width: 100%">SUBMIT</a-button>
                        </div>
                    </a-form>
                </a-col>
              
                <a-col :span="14" class="flex flex-col items-justify-between">
                        <!-- <div class="flex justify-end w-full"> -->
                            <a-form-item  class="flex justify-end w-full " label="Search Barcode" >
                                <a-input-search 
                                    v-model:value="barcode" 
                                    placeholder="Enter barcode..." 
                                    style="width: 100%"
                                    @change="onSearch" 
                                    />
                            </a-form-item>
                        <!-- </div> -->
                   
                    <a-spin tip="Searching Barcode..." :spinning="isloading" >
                        <a-card size="small" title="Lost Barcode List" class="w-full mt-4 "> 
                            <a-table 
                                bordered size="small" 
                                :dataSource="barcodes" 
                                :columns="columns"
                                :scroll="{ x: 'max-content' }"
                                style="width: 100%">

                                <template v-slot:bodyCell="{ column, record }">
                                    <template v-if="column.dataIndex === 'view'">
                                        <a-button type="dashed">
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
