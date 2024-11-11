<template>
    <div>
        <a-tabs style="font-weight: bold;">

            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <PieChartOutlined />
                        SPGC Redeem (Monthly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item :validate-status="monthlyRedeem.errors.SPGCDataType ? 'error' : ''"
                            :help="monthlyRedeem.errors.SPGCDataType">
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%; " placeholder="Select"
                                v-model:value="monthlyRedeem.SPGCDataType">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Store Sales</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item :validate-status="monthlyRedeem.errors.selectedStore ? 'error' : ''"
                            :help="monthlyRedeem.errors.selectedStore">
                            <div>
                                Store:
                            </div>
                            <a-select v-model:value="monthlyRedeem.selectedStore" style="width: 30%; "
                                placeholder="Select Store">
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Alturas Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Alturas Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Island City Mall">Island City
                                    Mall</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Plaza Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Alturas Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Colonade Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Colonade Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Alta Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Farmers Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Ubay Distribution Center">Ubay Distribution Center
                                </a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'"
                                    value="Screenville">Screenville</a-select-option>
                                <a-select-option v-if="monthlyRedeem.SPGCDataType === 'verifiedgc'" value="Asc Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="monthlyRedeem.errors.month ? 'error' : ''"
                            :help="monthlyRedeem.errors.month">
                            <div>
                                Month:

                            </div>
                            <a-select style="width: 30%; " placeholder="Select Month"
                                v-model:value="monthlyRedeem.month">
                                <a-select-option value="January">January</a-select-option>
                                <a-select-option value="February">February</a-select-option>
                                <a-select-option value="March">March</a-select-option>
                                <a-select-option value="April">April</a-select-option>
                                <a-select-option value="May">May</a-select-option>
                                <a-select-option value="June">June</a-select-option>
                                <a-select-option value="July">July</a-select-option>
                                <a-select-option value="August">August</a-select-option>
                                <a-select-option value="September">September</a-select-option>
                                <a-select-option value="October">October</a-select-option>
                                <a-select-option value="November">November</a-select-option>
                                <a-select-option value="December">December</a-select-option>

                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="monthlyRedeem.errors.year ? 'error' : ''"
                            :help="monthlyRedeem.errors.year">
                            <div>
                                Year:

                            </div>
                            <a-select style="width: 30%; " placeholder="Select Year" v-model:value="monthlyRedeem.year">
                                <a-select-option value="2017">2017</a-select-option>
                                <a-select-option value="2018">2018</a-select-option>
                                <a-select-option value="2019">2019</a-select-option>
                                <a-select-option value="2020">2020</a-select-option>
                                <a-select-option value="2021">2021</a-select-option>
                                <a-select-option value="2022">2022</a-select-option>
                                <a-select-option value="2023">2023</a-select-option>
                                <a-select-option value="2024">2024</a-select-option>

                            </a-select>
                        </a-form-item>
                    </div>
                    <div>
                        <a-button @click="monthlySubmitButton" style="background-color:#1e90ff; color:white">
                            <SendOutlined /> Submit
                        </a-button>
                    </div>
                </a-card>
            </a-tab-pane>

            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <PieChartOutlined />
                        SPGC Redeem (Yearly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item :validate-status="yearlyRedeem.errors.SPGCDataTypeYearly ? 'error' : ''"
                            :help="yearlyRedeem.errors.SPGCDataTypeYearly">
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%; " placeholder="Select"
                                v-model:value="yearlyRedeem.SPGCDataTypeYearly">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Store Sales</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item :validate-status="yearlyRedeem.errors.selectedStore ? 'error' : ''"
                            :help="yearlyRedeem.errors.selectedStore">
                            <div>
                                Store:

                            </div>
                            <a-select v-model:value="yearlyRedeem.selectedStore" style="width: 30%; "
                                placeholder="Select Store">
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Alturas Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Alturas Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Island City Mall">Island City
                                    Mall</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Plaza Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Alturas Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Colonade Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Colonade Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Alta Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Farmers Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Ubay Distribution Center">Ubay Distribution Center
                                </a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'"
                                    value="Screenville">Screenville</a-select-option>
                                <a-select-option v-if="yearlyRedeem.SPGCDataTypeYearly === 'verifiedgc'" value="Asc Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="yearlyRedeem.errors.year ? 'error': ''"
                            :help="yearlyRedeem.errors.year">
                            <div>
                                Year:
                            </div>
                            <a-select style="width: 30%; " placeholder="Select Year" v-model:value="yearlyRedeem.year">
                                <a-select-option value="2017">2017</a-select-option>
                                <a-select-option value="2018">2018</a-select-option>
                                <a-select-option value="2019">2019</a-select-option>
                                <a-select-option value="2020">2020</a-select-option>
                                <a-select-option value="2021">2021</a-select-option>
                                <a-select-option value="2022">2022</a-select-option>
                                <a-select-option value="2023">2023</a-select-option>
                                <a-select-option value="2024">2024</a-select-option>
                            </a-select>
                        </a-form-item>
                    </div>
                    <div>
                        <a-button @click="yearlyRedeemButton" style="background-color: #1e90ff; color:white">
                            <SendOutlined /> Submit
                        </a-button>
                    </div>
                </a-card>
            </a-tab-pane>

        </a-tabs>
    </div>
</template>
<script>
// import { defineComponent } from '@vue/composition-api'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SpgcLedger from '../Finance/SpgcLedger.vue';

export default {
    layout: AuthenticatedLayout,
    data() {
        return {
            monthlyRedeem: {
                year: '',
                month: '',
                selectedStore: '',
                SPGCDataType: '',
                errors: {}

            },
            yearlyRedeem: {
                SPGCDataTypeYearly: '',
                selectedStore: '',
                year: '',
                errors: {}

            }


        }
    },
    methods: {
        monthlySubmitButton() {
            this.monthlyRedeem.errors = {};
            const { year, month, selectedStore, SPGCDataType } = this.monthlyRedeem;

            if (!year) this.monthlyRedeem.errors.year = "Year field is required";
            if (!month) this.monthlyRedeem.errors.month = "Month field is required";
            if (!selectedStore) this.monthlyRedeem.errors.selectedStore = "Store field is required";
            if (!SPGCDataType) this.monthlyRedeem.errors.SPGCDataType = "Data type field is required";

            if (this.monthlyRedeem.errors.year || this.monthlyRedeem.errors.month || this.monthlyRedeem.errors.selectedStore || this.monthlyRedeem.errors.SPGCDataType) {
                return;
            };

            const monthlyData = {
                year: year,
                month: month,
                selectedStore: selectedStore,
                SPGCDataType: SPGCDataType
            }

            console.log(monthlyData);
            this.$inertia.get(route('storeaccounting.monthlyRedeemSubmit'), monthlyData);
        },
        yearlyRedeemButton() {
            this.yearlyRedeem.errors = {};
            const { SPGCDataTypeYearly, selectedStore, year } = this.yearlyRedeem;

            if (!SPGCDataTypeYearly) this.yearlyRedeem.errors.SPGCDataTypeYearly = "Data Type field is required";
            if (!selectedStore) this.yearlyRedeem.errors.selectedStore = "Store field is required";
            if (!year) this.yearlyRedeem.errors.year = "Year field is required";

            if (this.yearlyRedeem.errors.SPGCDataTypeYearly || this.yearlyRedeem.errors.selectedStore || this.yearlyRedeem.errors.year) {
                return;
            };

            const yearlyData = {
                SPGCDataTypeYearly: SPGCDataTypeYearly,
                selectedStore: selectedStore,
                year:year
            }
            console.log(yearlyData);
            this.$inertia.get(route('storeaccounting.yearlyRedeemSubmit'), yearlyData)
            // alert(1)
        }

    }
}
</script>
