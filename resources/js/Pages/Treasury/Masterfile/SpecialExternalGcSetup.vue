<template>
    <AuthenticatedLayout>
        <Head :title="title" />
        <a-breadcrumb style="margin: 15px 0">
            <a-breadcrumb-item>
                <Link :href="route(dashboardRoute)">Home</Link>
            </a-breadcrumb-item>
            <a-breadcrumb-item>{{ title }}</a-breadcrumb-item>
        </a-breadcrumb>
        <a-card>
            <div class="flex justify-between mb-5">
                <div>
                    <a-range-picker v-model:value="form.date" />
                </div>
                <div>
                    <a-input-search
                        class="mr-1"
                        v-model:value="form.search"
                        placeholder="Search here..."
                        style="width: 300px"
                    />
                    <a-button type="primary" @click="visible = true">
                        <template #icon>
                            <UserAddOutlined />
                        </template>
                        Add Customer Info
                    </a-button>
                </div>
            </div>
            <a-table
                :data-source="data.data"
                :columns="columns"
                bordered
                size="small"
                :pagination="false"
            >
                <template #title>
                    <a-typography-title :level="4">{{
                        title
                    }}</a-typography-title>
                </template>
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex">
                        <span
                            v-html="
                                highlightText(
                                    record[column.dataIndex],
                                    form.search,
                                )
                            "
                        >
                        </span>
                    </template>
                    <template v-if="column.key === 'createdBy'">
                        <span
                            v-html="
                                highlightText(
                                    record.user.full_name,
                                    form.search,
                                )
                            "
                        >
                        </span>
                    </template>
                </template>
            </a-table>

            <pagination-resource class="mt-5" :datarecords="data" />
        </a-card>

        <a-modal
            v-model:open="visible"
            title="Add a new Customer"
            ok-text="Create"
            cancel-text="Cancel"
            @ok="onOk"
        >
            <a-form
                ref="formRef"
                :model="formState"
                layout="vertical"
                name="form_in_modal"
            >
                <a-form-item
                    name="company"
                    label="Company Name"
                    has-feedback
                    :validate-status="
                        formState.invalid('company') ? 'error' : ''
                    "
                    :help="formState.errors.company"
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Company Name',
                        },
                    ]"
                    @change="formState.validate('company')"
                >
                    <a-input v-model:value="formState.company" />
                </a-form-item>
                <a-form-item
                    name="accountName"
                    label="Account Name"
                    has-feedback
                    :validate-status="
                        formState.invalid('accountName') ? 'error' : ''
                    "
                    :help="formState.errors.accountName"
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Account Name',
                        },
                    ]"
                    @change="formState.validate('accountName')"
                >
                    <a-input v-model:value="formState.accountName" />
                </a-form-item>
                <a-form-item
                    name="address"
                    label="Address"
                    has-feedback
                    :validate-status="
                        formState.invalid('address') ? 'error' : ''
                    "
                    :help="formState.errors.address"
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Address',
                        },
                    ]"
                    @change="formState.validate('address')"
                >
                    <a-input v-model:value="formState.address" />
                </a-form-item>
                <a-form-item
                    name="contactPerson"
                    label="Contact Person"
                    has-feedback
                    :validate-status="
                        formState.invalid('contactPerson') ? 'error' : ''
                    "
                    :help="formState.errors.contactPerson"
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Contact Person',
                        },
                    ]"
                    @change="formState.validate('contactPerson')"
                >
                    <a-input v-model:value="formState.contactPerson" />
                </a-form-item>
                <a-form-item
                    name="contactNumber"
                    label="Contact Number"
                    has-feedback
                    :validate-status="
                        formState.invalid('contactNumber') ? 'error' : ''
                    "
                    :help="formState.errors.contactNumber"
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Contact Number',
                        },
                    ]"
                    @change="formState.validate('contactNumber')"
                >
                    <a-input v-model:value="formState.contactNumber" />
                </a-form-item>
                <a-form-item
                    name="customerType"
                    label="Customer Type"
                    has-feedback
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Customer Type',
                        },
                    ]"
                    :validate-status="
                        formState.invalid('customerType') ? 'error' : ''
                    "
                    :help="formState.errors.customerType"
                >
                    <ant-select
                        :options="[
                            {
                                value: '2',
                                label: 'External',
                            },
                            {
                                value: '1',
                                label: 'Internal',
                            },
                        ]"
                        @handle-change="handleCustomerType"
                    />
                </a-form-item>
            </a-form>
        </a-modal>
    </AuthenticatedLayout>
</template>
<script lang="ts" setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import { highlighten, onProgress } from "@/Mixin/UiUtilities";
import { useForm } from "laravel-precognition-vue";
import { router } from "@inertiajs/core";
import {
    ColumnTypes,
    FilterTypes,
    SpecialExternalCustomer,
} from "@/types/treasury";
import { computed, ref, watch } from "vue";

const { openLeftNotification } = onProgress();
const { highlightText } = highlighten();

const props = defineProps<{
    title: string;
    data: { data: SpecialExternalCustomer[] };
    columns: ColumnTypes[];
    filters: FilterTypes;
}>();

const visible = ref(false);

const form = ref({
    search: props.filters.search,
    date: props.filters.date
        ? [dayjs(props.filters.date[0]), dayjs(props.filters.date[1])]
        : [],
});

const formState = useForm(
    "post",
    route("treasury.masterfile.addSpecialExternalCustomer"),
    {
        company: "",
        customerType: "",
        accountName: "",
        address: "",
        contactPerson: "",
        contactNumber: "",
    },
);

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

const handleCustomerType = (val) => {
    formState.validate("customerType");
    formState.customerType = val;
};

const onOk = () => {
    formState.submit({
        onSuccess: ({ data }) => {
            openLeftNotification(data);
            formState.reset();
            visible.value = false;
            router.visit(route(route().current()), { only: ["data"] });
        },
    });
};
const debouncedHandler = debounce(function () {
    const formattedDate = form.value.date
        ? form.value.date.map((date) => date.format("YYYY-MM-DD"))
        : [];

    router.get(
        route(route().current()),
        { ...pickBy(form.value), date: formattedDate },
        {
            preserveState: true,
        },
    );
}, 600);

watch(() => form.value, debouncedHandler, { deep: true });
</script>
