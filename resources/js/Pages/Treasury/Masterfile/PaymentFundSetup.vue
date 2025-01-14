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
                    Add Payment
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
                <template v-if="column.key === 'user'">
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
        title="Add a Payment Fund"
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
                name="paymentFundName"
                label="Payment Fund Name"
                has-feedback
                :validate-status="
                    formState.invalid('paymentFundName') ? 'error' : ''
                "
                :help="formState.errors.paymentFundName"
                :rules="[
                    {
                        required: true,
                        message: 'Please input the Payment Fund Name',
                    },
                ]"
                @change="formState.validate('paymentFundName')"
            >
                <a-input v-model:value="formState.paymentFundName" />
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
                route("treasury.masterfile.addPaymentFund"),
                {
                    paymentFundName: "",
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
