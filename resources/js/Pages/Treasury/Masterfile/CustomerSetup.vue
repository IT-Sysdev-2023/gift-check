<template>
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
                <a-typography-title :level="4">{{ title }}</a-typography-title>
            </template>
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex">
                    <span
                        v-html="
                            highlightText(record[column.dataIndex], form.search)
                        "
                    >
                    </span>
                </template>
                <template v-if="column.key === 'gcType'">
                    <span
                        v-html="
                            highlightText(record.gcType?.gctype, form.search)
                        "
                    >
                    </span>
                </template>
                <template v-if="column.key === 'createdBy'">
                    <span
                        v-html="
                            highlightText(record.user.full_name, form.search)
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
                :validate-status="formState.invalid('gcType') ? 'error' : ''"
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
</template>
<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import debounce from "lodash/debounce";
import pickBy from "lodash/pickBy";
import { highlighten } from "@/Mixin/UiUtilities";
import { useForm } from "laravel-precognition-vue";
import { onProgress } from "@/Mixin/UiUtilities";
import { router } from "@inertiajs/core";

const { openLeftNotification } = onProgress();
export default {
    layout: AuthenticatedLayout,
    setup() {
        const { highlightText } = highlighten();
        return { highlightText };
    },
    props: {
        title: String,
        data: Object,
        columns: Array,
        filters: Object,
    },
    data() {
        return {
            visible: false,
            form: {
                search: this.filters.search,
                date: this.filters.date
                    ? [dayjs(this.filters.date[0]), dayjs(this.filters.date[1])]
                    : [],
            },
            formState: useForm(
                "post",
                route("treasury.masterfile.addCustomer"),
                {
                    customerName: "",
                    customerType: "",
                    gcType: "",
                }
            ),
        };
    },
    computed: {
        dashboardRoute() {
            const webRoute = route().current();
            const res = webRoute?.split(".")[0];
            return res + ".dashboard";
        },
    },
    methods: {
        handleCustomerType(val) {
            this.formState.validate("customerType");
            this.formState.customerType = val;
        },
        handleGcType(val) {
            this.formState.validate("gcType");
            this.formState.gcType = val;
        },
        onOk() {
            this.formState.submit({
                preserveScroll: true,
                onSuccess: ({data}) => {
                    openLeftNotification(data);
                    this.formState.reset();
                    this.visible = false;
                    router.visit(route(route().current()), {only: ['data']})
                },
            });
        },
    },

    watch: {
        form: {
            deep: true,
            handler: debounce(function () {
                const formattedDate = this.form.date
                    ? this.form.date.map((date) => date.format("YYYY-MM-DD"))
                    : [];

                this.$inertia.get(
                    route(route().current()),
                    { ...pickBy(this.form), date: formattedDate },
                    {
                        preserveState: true,
                    }
                );
            }, 600),
        },
    },
};
</script>
