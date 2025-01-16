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
                        Add Customer
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
                    <template v-if="column.key === 'gcType'">
                        <span
                            v-html="
                                highlightText(
                                    record.gcType?.gctype,
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
                    name="customerName"
                    label="Customer Name"
                    has-feedback
                    :validate-status="
                        formState.invalid('customerName') ? 'error' : ''
                    "
                    :help="formState.errors.customerName"
                    :rules="[
                        {
                            required: true,
                            message: 'Please input the Customer Name',
                        },
                    ]"
                    @change="formState.validate('customerName')"
                >
                    <a-input v-model:value="formState.customerName" />
                </a-form-item>
                <a-form-item
                    name="customerType"
                    label="Customer Type"
                    has-feedback
                    :validate-status="
                        formState.invalid('customerType') ? 'error' : ''
                    "
                    :help="formState.errors.customerType"
                >
                    <ant-select
                        :options="[
                            {
                                value: 'external',
                                label: 'External',
                            },
                            {
                                value: 'internal',
                                label: 'Internal',
                            },
                        ]"
                        @handle-change="handleCustomerType"
                    />
                </a-form-item>
                <a-form-item
                    name="gcType"
                    label="Gc Type"
                    has-feedback
                    :validate-status="
                        formState.invalid('gcType') ? 'error' : ''
                    "
                    :help="formState.errors.gcType"
                >
                    <ant-select
                        :options="[
                            {
                                value: '1',
                                label: 'Regular',
                            },
                            {
                                value: '4',
                                label: 'Promo',
                            },
                        ]"
                        @handle-change="handleGcType"
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
import { ref, computed, watch } from "vue";
import { ColumnTypes, InstitutCustomer, FilterTypes } from "@/types/treasury";

const props = defineProps<{
    title: string;
    data: InstitutCustomer[];
    columns: ColumnTypes[];
    filters: FilterTypes;
}>();

const { openLeftNotification } = onProgress();

const { highlightText } = highlighten();

const visible = ref(false);
const form = ref({
    search: props.filters.search,
    date: props.filters.date
        ? [dayjs(props.filters.date[0]), dayjs(props.filters.date[1])]
        : [],
});

const formState = useForm("post", route("treasury.masterfile.addCustomer"), {
    customerName: "",
    customerType: "",
    gcType: "",
});

const dashboardRoute = computed(() => {
    const webRoute = route().current();
    const res = webRoute?.split(".")[0];
    return res + ".dashboard";
});

const handleCustomerType = (val) => {
    formState.validate("customerType");
    formState.customerType = val;
};

const handleGcType = (val) => {
    formState.validate("gcType");
    formState.gcType = val;
};

const onOk = () => {
    console.log("object");
    formState.submit({
        preserveScroll: true,
        onSuccess: ({ data }) => {
            openLeftNotification(data);
            formState.reset();
            visible.value = false;
            router.visit(route(route().current()), { only: ["data"] });
        },
    });
};

const debouncedHandler = debounce(() => {
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
