<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route('iad.dashboard')">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>

        <a-card :title="title" class="mt-10">
            <a-form
                ref="formRef"
                :model="formState"
                :label-col="{ span: 8 }"
                :wrapper-col="{ span: 12 }"
                @finish="onSubmit"
            >
                <a-row>
                    <a-col :span="10">
                        <a-form-item label="Date Allocated:" name="name">
                            <a-input v-model:value="currentDate" readonly />
                        </a-form-item>
                        <a-form-item
                            label="Store:"
                            name="store"
                            :validate-status="
                                getErrorStatus(formState, 'store')
                            "
                            :help="getErrorMessage(formState, 'store')"
                        >
                            <ant-select
                                :options="stores"
                                @handle-change="handleStoreChange"
                            />
                        </a-form-item>
                        <a-form-item
                            label="GC Type:"
                            name="gctype"
                            :validate-status="
                                getErrorStatus(formState, 'gcType')
                            "
                            :help="getErrorMessage(formState, 'gcType')"
                        >
                            <ant-select
                                :value="1"
                                :options="gcTypes"
                                @handle-change="handleGcTypeChange"
                            />
                        </a-form-item>

                        <a-card>
                            <a-row :gutter="16" class="text-center">
                                <a-col :span="12">
                                    <span>Denomination</span>
                                </a-col>
                                <a-col :span="12">
                                    <span>Quantity</span>
                                </a-col>
                            </a-row>
                            <a-row
                                :gutter="16"
                                class="mt-5"
                                v-for="(item, index) of formState.denomination"
                                :key="index"
                            >
                                <a-col :span="12">
                                    <a-input
                                        :value="item.denomination"
                                        readonly
                                        class="text-end"
                                    />
                                </a-col>
                                <a-col :span="12" style="text-align: center">
                                    <a-input-number
                                        id="inputNumber"
                                        v-model:value="item.qty"
                                        placeholder="0"
                                        :min="0"
                                    >
                                        <template #upIcon>
                                            <ArrowUpOutlined />
                                        </template>
                                        <template #downIcon>
                                            <ArrowDownOutlined />
                                        </template>
                                    </a-input-number>
                                </a-col>
                            </a-row>
                            <span
                                v-if="formState.errors.denomination"
                                class="text-red-500"
                                >{{ formState.errors.denomination }}</span
                            >
                        </a-card>

                        <a-form-item class="text-end mt-5">
                            <a-button type="primary" html-type="submit"
                                >Submit</a-button
                            >
                        </a-form-item>
                    </a-col>
                    <a-col :span="14">
                        <a-row :gutter="16">
                            <a-col :span="12">
                                <a-card title="Validated GC for Allocation">
                                    <a-alert
                                        v-for="(item, index) of denoms"
                                        :key="index"
                                        class="mt-5"
                                        :message="`₱ ${item.denomination}`"
                                        type="success"
                                    >
                                        <template #action>
                                            <a-space>
                                                <span>
                                                    {{ item.cnt }}
                                                </span>
                                            </a-space>
                                        </template>
                                    </a-alert>
                                </a-card>
                            </a-col>
                            <a-col :span="12">
                                <a-card
                                    :title="allocatedGc + ' (Allocated GC)'"
                                    v-if="allDenoms"
                                >
                                    <a-alert
                                        v-for="(item, index) of allDenoms"
                                        :key="index"
                                        class="mt-5"
                                        :message="`₱ ${item.denomination}`"
                                        type="warning"
                                    >
                                        <template #action>
                                            <a-space>
                                                <span>
                                                    {{ item.count }}
                                                </span>
                                            </a-space>
                                        </template>
                                    </a-alert>
                                    <a-button
                                        class="float-right mt-5"
                                        @click="viewAllocatedGc"
                                    >
                                        View Allocated Gc
                                    </a-button>
                                </a-card>
                            </a-col>
                        </a-row>
                    </a-col>
                </a-row>
            </a-form>
        </a-card>
        <ant-modal-table
            v-model:open="openModal"
            title="Allocated Gc"
            :columns="columns"
            :data="allocatedData"
        />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import dayjs from "dayjs";
import axios from "axios";
import { getError, onProgress } from "@/Mixin/UiUtilities";

const props = defineProps<{
    title: string;
    stores: { value: number; label: string }[];
    gcTypes: { value: number; label: string }[];
    denoms: any[];
}>();

const allocatedData = ref([]);
const allocatedGc = ref(null);
const allDenoms = ref(null);
const currentDate = dayjs().format("MMM DD, YYYY");
const openModal = ref(false);

const formState = useForm<{
    store: number;
    gcType: number;
    denomination: any[];
}>({
    store: 0,
    gcType: 1,
    denomination: props.denoms.map((item) => ({
        denomination: item.denomination,
        qty: 0,
        denom_id: item.denom_id,
    })),
});
const columns = [
    {
        title: "GC Barcode No",
        dataIndex: "loc_barcode_no",
    },
    {
        title: "Date Allocated",
        dataIndex: "loc_date",
    },
    {
        title: "Allocated By",
        key: "fullname",
    },
    {
        title: "GC Type",
        key: "gctype",
    },
    {
        title: "Production #",
        key: "productionrequest",
    },
    {
        title: "Denom",
        key: "denom",
    },
];

const handleStoreChange = async (
    value: number,
    obj: { value: number; label: string }
) => {
    clearError(formState, "store");
    allocatedGc.value = obj.label;
    formState.store = obj.value;

    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.storeAllocation"),
        { params: { store: formState.store, type: formState.gcType } }
    );
    allDenoms.value = data;
};

const handleGcTypeChange = async (value: number) => {
    clearError(formState, "gcType");
    formState.gcType = value;

    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.storeAllocation"),
        { params: { store: formState.store, type: formState.gcType } }
    );
    allDenoms.value = data;
    
};
const { openLeftNotification } = onProgress();
const onSubmit = () => {
    formState
        .transform((data) => ({
            ...data,
            denomination: data.denomination.filter(
                (item) => item.denomination !== 0 && item.qty !== 0
            ),
        }))
        .post(route("treasury.transactions.gcallocation.store"), {
            onSuccess: ({ props }) => {
                openLeftNotification(props.flash);
                router.get(route('treasury.dashboard'));
            },
        });
};

const viewAllocatedGc = async () => {
    const { data } = await axios.get(
        route("treasury.transactions.gcallocation.viewAllocatedGc"),
        {
            params: {
                store: formState.store,
                type: formState.gcType,
            },
        }
    );
    allocatedData.value = data;
    openModal.value = true;
};

const { getErrorMessage, getErrorStatus, clearError } = getError();
</script>
