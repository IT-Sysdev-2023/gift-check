<template>
    <a-tabs>
        <a-tab-pane key="1">
            <template #tab>
                <span>
                    <CheckOutlined />
                    Check Variances
                </span>
            </template>
            <a-card>
                <div>
                    Customer Name:
                </div>

                <a-form-item :validate-status="varianceData.errors.customerName ? 'error' : ''"
                    :help="varianceData.errors.customerName" style="width: 35%;">

                    <a-select v-model:value="varianceData.customerName" style="border: 1px solid #1e90ff; background-color: #1e90ff;">
                        <a-select-option v-for="item in customer"
                            :key="`${item.spcus_companyname}-${item.spcus_acctname}`"
                            :value="`${item.spcus_companyname} - ${item.spcus_acctname}`">
                            {{ `${item.spcus_companyname} - ${item.spcus_acctname}` }}
                        </a-select-option>
                    </a-select>
                </a-form-item>

                <div style="margin-top: 10px;">
                    <a-button style=" background-color: #1e90ff; color:white;" @click="generateButton">
                        <DeploymentUnitOutlined />
                        Generate
                    </a-button>
                </div>
            </a-card>
        </a-tab-pane>
    </a-tabs>
    <!-- {{ customer }} -->
</template>

<script>
// import { defineComponent } from '@vue/composition-api'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    layout: AuthenticatedLayout,
    props: {
        customer: Object
    },
    data() {
        return {
            varianceData: this.$inertia.form({
                customerName: '',
                errors: {}

            })
        }
    },
    methods: {
        generateButton() {
            this.varianceData.errors = {};

            if (!this.varianceData.customerName) {
                this.varianceData.errors.customerName = "Customer Name field is required";
                return;
            }


            this.$inertia.get(route('CheckVariance'), {
                customerData: this.varianceData.customerName

            })
        }
    }

}
</script>
