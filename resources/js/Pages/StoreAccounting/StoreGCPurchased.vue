<template>
    <div>
        <a-tabs style="font-weight: bold;">

            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <MoneyCollectOutlined />
                        Billing Between Stores and BU (Monthly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item :validate-status="billMonthly.errors.StoreDataType ? 'error' : ''"
                            :help="billMonthly.errors.StoreDataType">
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%; "
                                placeholder="Select" v-model:value="billMonthly.StoreDataType">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Store Sales</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item :validate-status="billMonthly.errors.selectedStore ? 'error' : ''"
                            :help="billMonthly.errors.selectedStore">
                            <div>
                                Store:
                            </div>
                            <a-select v-model:value="billMonthly.selectedStore"
                                style="width: 30%; "
                                placeholder="Select Store">
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Alturas Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Alturas Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Island City Mall">Island City
                                    Mall</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Plaza Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Alturas Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Colonade Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Colonade Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Alta Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Farmers Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Ubay Distribution Center">Ubay
                                    Distribution Center</a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'"
                                    value="Screenville">Screenville
                                </a-select-option>
                                <a-select-option v-if="billMonthly.StoreDataType === 'verifiedgc'" value="Asc Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="billMonthly.errors.month ? 'error' : ''"
                            :help="billMonthly.errors.month">
                            <div>
                                Month:

                            </div>
                            <a-select style="width: 30%; "
                                placeholder="Select Month" v-model:value="billMonthly.month">
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

                        <a-form-item :validate-status="billMonthly.errors.year ? 'error' : ''"
                            :help="billMonthly.errors.year">
                            <div>
                                Year:

                            </div>
                            <a-select style="width: 30%; "
                                placeholder="Select Year" v-model:value="billMonthly.year">
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
                        <MoneyCollectOutlined />
                        Billing Between Stores and BU (Yearly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item :validate-status="billYearly.errors.storeYearlyData ? 'error' : ''"
                            :help="billYearly.errors.storeYearlyData">
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%; "
                                placeholder="Select" v-model:value="billYearly.storeYearlyData">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Store Sales</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item :validate-status="billYearly.errors.selectedStore ? 'error' : ''"
                            :help="billYearly.errors.selectedStore">
                            <div>
                                Store:

                            </div>
                            <a-select v-model:value="billYearly.selectedStore"
                                style="width: 30%; "
                                placeholder="Select Store">
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Alturas Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Altura Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Island City Mall">Island
                                    City
                                    Mall</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Plaza Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Alturas Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Colonade Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Colonade Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Alta Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Farmers Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="Ubay Distribution Center">Ubay Distribution
                                    Center</a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'"
                                    value="store11">Screenville
                                </a-select-option>
                                <a-select-option v-if="billYearly.storeYearlyData === 'verifiedgc'" value="Asc Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="billYearly.errors.year ? 'error' : ''"
                            :help="billYearly.errors.year">
                            <div>
                                Year:
                            </div>
                            <a-select style="width: 30%; "
                                placeholder="Select Year" v-model:value="billYearly.year">
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
                        <a-button @click="yearlySubmitButton" style="background-color: #1e90ff; color:white">
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

export default {
    layout: AuthenticatedLayout,
    data() {
        return {
            billMonthly: {
                year: '',
                month: '',
                selectedStore: '',
                StoreDataType: '',
                errors: {}


            },
            billYearly: {
                year: '',
                storeYearlyData: '',
                selectedStore: '',
                errors: {}

            }
        }

    },
    methods: {
        monthlySubmitButton() {
            // alert(1)
            this.billMonthly.errors = {};
            const { year, month, selectedStore, StoreDataType } = this.billMonthly;
            if (!year) this.billMonthly.errors.year = "Year field is required";
            if (!month) this.billMonthly.errors.month = "Month field is required";
            if (!selectedStore) this.billMonthly.errors.selectedStore = "Store field is required";
            if (!StoreDataType) this.billMonthly.errors.StoreDataType = "Data type field is required";

            if (this.billMonthly.errors.year || this.billMonthly.errors.month || this.billMonthly.errors.selectedStore || this.billMonthly.errors.StoreDataType) {
                return;
            };

            const monthlyData = {
                year: year,
                month: month,
                selectedStore: selectedStore,
                StoreDataType: StoreDataType
            }
            console.log(monthlyData);
            this.$inertia.get(route('storeaccounting.billingMonthlySubmit'), monthlyData);
        },
        yearlySubmitButton() {
            this.billYearly.errors = {};
            const { year, storeYearlyData, selectedStore } = this.billYearly;
            if (!year) this.billYearly.errors.year = "Year field is required";
            if (!storeYearlyData) this.billYearly.errors.storeYearlyData = "Data type field is required";
            if (!selectedStore) this.billYearly.errors.selectedStore = "Store field is required";

            if (this.billYearly.errors.year || this.billYearly.errors.storeYearlyData || this.billYearly.errors.selectedStore) {
                return;
            };

            const yearlyData = {
                year: year,
                storeYearlyData: storeYearlyData,
                selectedStore: selectedStore
            }
            console.log(yearlyData);
            this.$inertia.get(route('storeaccounting.billingYearlySubmit'), yearlyData);
        }

    }
}
</script>
