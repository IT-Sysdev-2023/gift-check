<template>
    <a-card title="Store GC Request Form">
        <div class="flex justify-end mb-2">
            <a-button @click="submitForm" type="primary">
                <CheckOutlined /> Submit Form
            </a-button>
        </div>

        <a-row :gutter="[16, 16]">
            <a-col :span="8">
                <a-card>
                    <a-form-item label="GC Request No">
                        <a-input
                            v-model:value="form.requestNumber"
                            readonly
                        ></a-input>
                    </a-form-item>
                    <a-form-item label="Retail Store">
                        <a-input
                            v-model:value="form.storeName"
                            readonly
                        ></a-input>
                    </a-form-item>
                    <a-form-item label="Date Requested">
                        <a-input
                            v-model:value="form.dateReq"
                            readonly
                        ></a-input>
                    </a-form-item>
                    <a-form-item
                        label="Remarks"
                        has-feedback
                        :help="error?.remarks"
                        :validate-status="error?.remarks ? 'error' : ''"
                    >
                        <a-textarea v-model:value="form.remarks"></a-textarea>
                    </a-form-item>
                    <a-form-item label="Prepared by">
                        <a-input
                            v-model:value="form.approvedBy"
                            readonly
                        ></a-input>
                    </a-form-item>
                </a-card>
            </a-col>

            <a-col :span="8">
                <a-card>
                    <a-form-item
                        label=""
                        has-feedback
                        :help="error?.quantities"
                        :validate-status="error?.quantities ? 'error' : ''"
                    >
                        <a-table
                            :dataSource="denoms"
                            :columns="denomColumns"
                            :pagination="false"
                        >
                            <template #bodyCell="{ column, record, index }">
                                <template v-if="column.key === 'qty'">
                                    <a-input
                                        type="number"
                                        v-model:value="form.quantities[record.denom_id]"
                                        @change="handleQuantity(record)"
                                    ></a-input>
                                </template>
                            </template>
                        </a-table>
                        <div class="flex justify-end">Total: {{ total }}</div>
                    </a-form-item>
                </a-card>
            </a-col>
            <a-col :span="8">
                <a-table
                    :dataSource="allocated"
                    :columns="allocatedGcColumns"
                    :pagination="false"
                    bordered
                >
                    <template #bodyCell="{ column, record }">
                        <template v-if="column.dataIndex === 'qty'">
                            <a-input :value="record.count"></a-input>
                        </template>
                    </template>
                </a-table>
            </a-col>
        </a-row>
    </a-card>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import { notification } from "ant-design-vue";
import { useForm } from "@inertiajs/vue3";
import { reactive, watch } from "vue";

export default {
    layout: AuthenticatedLayout,
    props: {
        denoms: Array,
        denomColumns: Array,
        requestno: String,
        storeName: String,
        allocatedGcColumns: String,
        requestNumber: Number,
        allocated: Object,
    },
    data() {
        return {
            file: null,
            error: null,
            form: useForm({
                sgc_id: this.requestno?.sgc_id,
                quantities: reactive({}),  // Make quantities reactive
                requestNumber: this.requestNumber,
                storeName: this.storeName[0]["store_name"],
                dateReq: dayjs().format("YYYY-MM-DD"),
                dateNeed: "",
                remarks: "",
                approvedBy: this.$page.props.auth.user.full_name,
                approvedById: this.$page.props.auth.user.user_id,
            }),
            total: 0,
        };
    },
    mounted() {
        // Initialize quantities with denom_id keys and set to 0
        this.denoms.forEach((denom) => {
            this.form.quantities[denom.denom_id] = 0;
        });
    },
    methods: {
        disabledDate(current) {
            return current && current < new Date().setHours(0, 0, 0, 0);
        },
        calculateTotal() {
            // Calculate the total based on the updated quantities
            this.total = this.denoms.reduce((sum, denom) => {
                const qty = this.form.quantities[denom.denom_id] || 0;
                return sum + denom.denomination * qty;
            }, 0);
        },
        handleQuantity(record) {
            // Trigger recalculation
            const newQty = this.form.quantities[record.denom_id];
            this.form.quantities[record.denom_id] = newQty;
            this.calculateTotal();
        },
        submitForm() {
            this.form.post(route("retail.gc.request.submit"), {
                onError: (e) => {
                    this.error = e;
                },
                onSuccess: (response) => {
                    notification[response.props.flash.type]({
                        message: response.props.flash.msg,
                        description: response.props.flash.description,
                    });

                    if (response.props.flash.type === "success") {
                        this.$inertia.get(route("retail.dashboard"));
                    }
                },
            });
        },
        handleImageChange(document) {
            this.file = document.file;
        },
    },
    watch: {
        // Watch the quantities object for changes and recalculate total
        'form.quantities': {
            handler: 'calculateTotal',
            deep: true,  // Ensure deep watching for nested object changes
        },
    },
};
</script>
