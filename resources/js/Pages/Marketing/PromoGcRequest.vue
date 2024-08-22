<template>

    <Head title="Promo GC Request Form" />
    <a-form>
        <a-card title="Promo GC Request Form"></a-card>
        <a-row :gutter="[16, 16]">
            <a-col :span="12" style="margin-top: 20px;">
                <a-card>
                    <a-form-item label="RFPROM No:" name="rfprom">
                        <a-input style="width: 6rem;" v-model:value="form.rfprom_number" readonly />
                    </a-form-item>
                    <a-form-item label="Date Requested" name="daterequested">
                        <a-date-picker :value="form.dateReq" readonly />
                    </a-form-item>
                    <a-form-item label="Date Needed" name="dateneeded">
                        <a-date-picker v-model:value="form.dateneeded" />
                    </a-form-item>
                    <a-form-item label="PWP/ Approved Budget Doc:" name="file">
                        <a-upload v-model:file-list="form.fileList" name="file" action="" :headers="headers"
                            @change="handleChange">
                            <a-button>
                                <upload-outlined></upload-outlined>
                                Click to Upload
                            </a-button>
                        </a-upload>
                    </a-form-item>
                    <a-form-item label="Remarks:" name="remarks">
                        <a-textarea v-model:value="form.remarks" allow-clear />
                    </a-form-item>
                    <a-form-item label="Promo Group:" name="promoGroup">
                        <a-select ref="select" placeholder="select" v-model:value="form.groups" style="width: 130px">
                            <a-select-option :value="'1'">Group 1</a-select-option>
                            <a-select-option :value="'2'">Group 2</a-select-option>
                        </a-select>
                    </a-form-item>
                    <div>
                        <p>Denomination</p>
                    </div>
                    <a-form-item label="₱ 100.00" name="0">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="form.quantities[0]"
                            :min="0" />
                    </a-form-item>
                    <a-form-item label="₱ 500.00" name="1">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="form.quantities[1]"
                            :min="0" />
                    </a-form-item>
                    <a-form-item label="₱ 1,000.00" name="2">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="form.quantities[2]"
                            :min="0" />
                    </a-form-item>
                    <a-form-item label="₱ 2,000.00" name="3">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="form.quantities[3]"
                            :min="0" />
                    </a-form-item>
                    <a-form-item label="₱ 5,000.00" name="4">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="form.quantities[4]"
                            :min="0" />
                    </a-form-item>
                    <a-form-item>
                        <a-button type="primary" @click="submitForm">Submit</a-button>
                    </a-form-item>

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
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import dayjs from "dayjs";
import pickBy from "lodash/pickBy";
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
    },
    data() {
        return {
            form: {
                dateReq: dayjs(this.dateRequested),
                rfprom_number: this.rfprom_number,
                dateneeded: dayjs(),
                fileList: null,
                groups: null,
                remarks: null,
                quantities: ['', '', '', '', ''],
                requestBy: this.$page.props.auth.user.user_id,
                totalDenom: 0,

            },
            totalValue: 0,
            denominations: [100, 500, 1000, 2000, 5000],
            headers: {},
        };
    },
    methods: {
        handleChange() {
            this.calculateTotal();
        },
        calculateTotal() {

            console.log(this.form.quantities);
            this.totalValue = this.form.quantities.reduce((acc, qty, index) => {
                return acc + (Number(qty) * this.denominations[index]);
            }, 0);

            this.form.totalDenom = this.totalValue;
        },
        submitForm() {
            const formattedDateReq = this.form.dateReq ? dayjs(this.form.dateReq).format("YYYY-MM-DD") : [];
            const formattedDateNeed = this.form.dateneeded ? dayjs(this.form.dateneeded).format("YYYY-MM-DD") : [];
            const filename = this.form.fileList.map((file) => file.name);

            this.$inertia.post(route('marketing.promo.gc.submit'),
                {
                    ...pickBy(this.form),
                    dateR: formattedDateReq,
                    dateN: formattedDateNeed,
                    fileName: filename
                }, {
                onSuccess: () => {
                    this.form = {
                        dateReq: dayjs(this.dateRequested),
                        rfprom_number: this.rfprom_number,
                        dateneeded: dayjs(),
                        fileList: null,
                        groups: null,
                        remarks: null,
                        quantities: ['', '', '', '', ''],
                        requestBy: this.$page.props.auth.user.user_id,
                        totalDenom: 0,

                    };
                    this.totalValue = 0;
                    this.denominations = [100, 500, 1000, 2000, 5000];
                    this.headers = {};

                    notification.success({
                        message: 'Hi, ' + this.$page.props.auth.user.full_name + '!',
                        description:
                            'Your Promo GC Request was successfully submitted',
                    });

                }
            }
            );
        },
    },

    watch: {
        form: {
            handler: 'calculateTotal',
            deep: true
        }
    }
};
</script>
