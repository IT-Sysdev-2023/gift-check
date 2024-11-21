<template>
    <AuthenticatedLayout>
        <a-card title="Cancelled Special GC Request">
            <div>
                <div class="flex justify-end mb-3">
                    <a-input-search
                        v-model:value="searchval"
                        placeholder="input search text"
                        style="width: 300px"
                        @change="onSearch"
                    />
                </div>
            </div>
            <a-spin :spinning="isloading">
                <a-table
                    :pagination="false"
                    size="small"
                    bordered
                    :dataSource="data.data"
                    :columns="columns"
                >
                    <template v-slot:bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'view'">
                            <a-button
                                type="primary"
                                @click="fetchdata(record.spexgc_id)"
                            >
                                <MenuUnfoldOutlined /> View
                            </a-button>
                        </template>
                    </template>
                </a-table>
            </a-spin>
            <Pagination :datarecords="data" class="mt-5" />
        </a-card>
        <a-modal
            width="900px"
            :footer="false"
            v-model:open="open"
            :title="'Request #: ' + selectedData.spexgc_num"
        >
            <a-row :gutter="[16, 16]">
                <a-col :span="12">
                    <a-card title="Request Details">
                        <a-form-item label="GC Type">
                            <a-input :value="type()" readonly />
                        </a-form-item>
                        <a-form-item label="Date Requested">
                            <a-input :value="dateReq()" readonly />
                        </a-form-item>
                        <a-form-item label="Amount">
                            <a-input
                                :value="selectedData.spexgc_amount"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Customer Name">
                            <a-textarea
                                :value="selectedData.spcus_companyname"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Request Remarks">
                            <a-textarea
                                :value="selectedData.spexgc_remarks"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Requested By">
                            <a-input
                                :value="selectedData.requested_by"
                                readonly
                            />
                        </a-form-item>
                    </a-card>
                </a-col>
                <a-col :span="12">
                    <a-card title="Cancel Details">
                        <a-form-item label="Cancelled Date">
                            <a-input
                                :value="selectedData.cancelledDate"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Cancel Remarks">
                            <a-input
                                :value="selectedData.cancelRemarks"
                                readonly
                            />
                        </a-form-item>
                        <a-form-item label="Cancelled By">
                            <a-input
                                :value="selectedData.cancelledBy"
                                readonly
                            />
                        </a-form-item>
                    </a-card>
                </a-col>
            </a-row>
        </a-modal>
    </AuthenticatedLayout>
</template>

<script setup>
import Pagination from "@/Components/Pagination.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/core";
import axios from "axios";
import dayjs from "dayjs";
import { ref } from "vue";

defineProps({ data: Object });
const open = ref(false);
const selectedData = ref({});
const searchval = ref("");
const isloading = ref(false);

const type = () => {
    return selectedData.spexgc_type == "1" ? "Internal" : "External";
};

const dateReq = () => {
    return dayjs(selectedData.spexgc_datereq).format("MMMM D, YYYY");
};

const onSearch = () => {
    router.get(
        route("finance.cancelledSpecialExternalGC.list"),
        {
            data: searchval.value,
        },
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
            preserveState: true,
        }
    );
};

const fetchdata = (id) => {
    axios
        .get(route("finance.cancelledSpecialExternalGC.view"), {
            params: {
                id: id,
            },
        })
        .then((r) => {
            selectedData.value = r.data;
            open.value = true;
        });
};

const columns = [
    {
        title: "Name",
        dataIndex: "spexgc_num",
        width: "10%",
    },
    {
        title: "Date Request",
        dataIndex: "spexgc_datereq",
    },
    {
        title: "Customer",
        dataIndex: "spcus_companyname",
        ellipsis: "true",
    },
    {
        title: "Date Cancelled",
        dataIndex: "cancelledDate",
    },
    {
        title: "Cancelled By",
        dataIndex: "cancelledBy",
    },
    {
        title: "View Details",
        dataIndex: "view",
        align: "center",
    },
];
</script>
