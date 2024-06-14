<script>
import { PlusOutlined, BarcodeOutlined } from "@ant-design/icons-vue";
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    layout: AuthenticatedLayout,
    components: {
        PlusOutlined,
        BarcodeOutlined
    },
    data() {
        return {
            rfprom: '',
            daterequested: '',
            dateneeded: '',
            fileList: [],
            headers: {},
            remarks: '',
            value1: '',
            quantities: ['', '', '', '', ''],
            denominations: [100, 500, 1000, 2000, 5000],
            totalValue: 0
        };
    },
    methods: {
        handleChange() {
            this.calculateTotal();
        },
        calculateTotal() {
            this.totalValue = this.quantities.reduce((acc, qty, index) => {
                // Ensure qty is treated as a number
                return acc + (Number(qty) * this.denominations[index]);
            }, 0);
        }
    },
    watch: {
        quantities: {
            handler: 'calculateTotal',
            deep: true
        }
    }
};
</script>

<template>
    <Head title="Promo GC Request Form" />
    <a-form>
        <a-card title="Promo GC Request Form"></a-card>
        <a-row :gutter="[16, 16]">
            <a-col :span="12">
                <a-card>
                    <a-form-item label="RFPROM No:" name="rfprom">
                        <a-input style="width: 6rem;" v-model:value="rfprom" disabled />
                    </a-form-item>
                    <a-form-item label="Date Requested" name="daterequested">
                        <a-date-picker v-model:value="daterequested" disabled />
                    </a-form-item>
                    <a-form-item label="Date Needed" name="dateneeded">
                        <a-date-picker v-model:value="dateneeded" />
                    </a-form-item>
                    <a-form-item label="PWP/ Approved Budget Doc:" name="file">
                        <a-upload v-model:file-list="fileList" name="file" action="" :headers="headers"
                            @change="handleChange">
                            <a-button>
                                <upload-outlined></upload-outlined>
                                Click to Upload
                            </a-button>
                        </a-upload>
                    </a-form-item>
                    <a-form-item label="Remarks:" name="remarks">
                        <a-textarea v-model:value="remarks" allow-clear />
                    </a-form-item>
                    <a-form-item label="Promo Group:" name="promoGroup">
                        <a-select ref="select" placeholder="select" v-model:value="value1" style="width: 120px">
                            <a-select-option value="Group1">Group 1</a-select-option>
                            <a-select-option value="Group2">Group 2</a-select-option>
                        </a-select>
                    </a-form-item>
                    <div>
                        <p>Denomination</p>
                    </div>
                    <a-form-item label="₱ 100.00" name="0">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="quantities[0]" :min="0" />
                    </a-form-item>
                    <a-form-item label="₱ 500.00" name="1">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="quantities[1]" :min="0" />
                    </a-form-item>
                    <a-form-item label="₱ 1,000.00" name="2">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="quantities[2]" :min="0" />
                    </a-form-item>
                    <a-form-item label="₱ 2,000.00" name="3">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="quantities[3]" :min="0" />
                    </a-form-item>
                    <a-form-item label="₱ 5,000.00" name="4">
                        <a-input type="number" placeholder="Enter Quantity" v-model:value="quantities[4]" :min="0" />
                    </a-form-item>
                    <a-form-item>
                        <a-button type="primary" html-type="submit">Submit</a-button>
                    </a-form-item>

                </a-card>
            </a-col>
            <a-col :span="12">
                <a-card>
                    <a-card title="Total Promo GC Request" :bordered="false">
                        <a-input :value="'₱ ' + totalValue" readonly style="font-size: xx-large;"></a-input>
                    </a-card>

                </a-card>
            </a-col>
        </a-row>
    </a-form>
</template>
