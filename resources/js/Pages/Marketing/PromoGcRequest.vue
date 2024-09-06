<template>

    <Head title="Promo GC Request Form" />
    <a-form>
        <a-card title="Promo GC Request Form"></a-card>
        <div class="flex justify-end mt-2">
            <a-button type="primary" @click="submit">
                Submit Promo Requestfgffftt
            </a-button>
        </div>
        <a-row :gutter="[16, 16]">
            <a-col :span="12" style="margin-top: 20px;">
                <a-card>
                    <a-form-item label="RFPROM No:" name="rfprom">
                        <a-input style="width: 6rem;" v-model:value="form.rfprom_number" readonly />
                    </a-form-item>
                    <a-form-item label="Date Requested" name="daterequested">
                        <a-date-picker :disabled-date="disabledDate" :value="form.dateReq" readonly />
                    </a-form-item>
                    <a-form-item label="Date Needed" name="dateneeded">
                        <a-date-picker v-model:value="form.dateneeded" :disabled-date="disabledDate" />
                    </a-form-item>
                    <a-form-item label="Remarks:" name="remarks">
                        <a-textarea v-model:value="form.remarks" allow-clear />
                    </a-form-item>
                    <a-form-item label="Promo Group:" name="promoGroup">
                        <a-select ref="select" placeholder="select" v-model:value="form.group" style="width: 130px">
                            <a-select-option :value="'1'">Group 1</a-select-option>
                            <a-select-option :value="'2'">Group 2</a-select-option>
                        </a-select>
                    </a-form-item>
                    <div>
                        <a-table :dataSource="denomination" :columns="denomCol" :pagination="false">
                            <template #bodyCell="{ column, record, index }">
                                <!-- {{ record.denom_id }} -->
                                <template v-if="column.dataIndex === 'qty'">
                                    <a-input type="number" v-model:value="form.quantities[record.denom_id]"
                                        @change="calculateTotal()" />
                                </template>
                            </template>
                        </a-table>
                    </div>
                </a-card>
            </a-col>
            <a-col :span="12" style="margin-top: 20px;">
                <a-card>
                    <a-card title="Total Promo GC Request" :bordered="false">
                        <a-input :value="'₱ ' + totalValue" readonly style="font-size: xx-large;"></a-input>
                    </a-card>

                </a-card>
                <a-form-item label="Prepared By:" name="prepby" class="mt-5">
                    <a-input v-model:value="$page.props.auth.user.full_name" readonly />
                    <a-input v-model:value="form.requestBy" class="hidden" />
                </a-form-item>
            </a-col>
        </a-row>
    </a-form>
</template>

<script>
import { PlusOutlined, BarcodeOutlined } from "@ant-design/icons-vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import { notification } from 'ant-design-vue';

export default {
    layout: AuthenticatedLayout,
    components: {
        PlusOutlined,
        BarcodeOutlined
    },
    props: {
        rfprom_number: String,
        dateRequested: String,
        denomination: Object,
        denomCol: Object
    },
    data() {
        return {
            form: {
                dateReq: dayjs(this.dateRequested),
                rfprom_number: this.rfprom_number,
                dateneeded: dayjs(),
                fileList: null,
                group: '1',
                remarks: null,
                quantities: [],
                requestBy: this.$page.props.auth.user.user_id,
                totalDenom: 0,

            },
            totalValue: 0,
            headers: {},
        };
    },
    methods: {
        calculateTotal() {
            let total = 0;
            this.denomination.forEach(record => {
                const quantity = this.form.quantities[record.denom_id] || 0;
                const subtotal = record.denomination * quantity;
                total += subtotal;
            });
            this.totalValue = total.toLocaleString();
        },
        submit() {
            this.$inertia.post(route('marketing.promo.gc.submit'), {
                data: this.form,
                total: this.totalValue
            }, {
                onSuccess: (response) => {
                    notification[response.props.flash.type]({
                        message: response.props.flash.msg,
                        description: response.props.flash.description,
                    })

                    if (response.props.flash.type == 'success') {
                        this.$inertia.get(route('marketing.dashboard'));
                    }
                }
            })
        },
        disabledDate(current) {
            return current && current < new Date().setHours(0, 0, 0, 0);
        },
    }
};
</script>
