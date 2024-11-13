<template>
    <div>
        <a-tabs style="font-weight: bold;">

            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <BankOutlined />
                        Verified Store Purchased (Monthly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item :validate-status="verifiedMonthly.errors.VerifiedStoreDataType ? 'error' : ''"
                            :help="verifiedMonthly.errors.VerifiedStoreDataType">
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%;" placeholder="Select"
                                v-model:value="verifiedMonthly.VerifiedStoreDataType">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Store Sales</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item :validate-status="verifiedMonthly.errors.selectedStore ? 'error' : ''"
                            :help="verifiedMonthly.errors.selectedStore">
                            <div>
                                Store:
                            </div>
                            <a-select style="width: 30%;" placeholder="Select Store"
                                v-model:value="verifiedMonthly.selectedStore">
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Alturas Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Alturas Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Island City Mall">Island City
                                    Mall</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Plaza Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Alturas Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Colonade Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Colonade Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Alta Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Farmers Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Ubay Distribution Center">
                                    Ubay Distribution Center</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Screenville">Screenville</a-select-option>
                                <a-select-option v-if="verifiedMonthly.VerifiedStoreDataType === 'verifiedgc'"
                                    value="Asc Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="verifiedMonthly.errors.month ? 'error' : ''"
                            :help="verifiedMonthly.errors.month">
                            <div>
                                Month:

                            </div>
                            <a-select style="width: 30%;" placeholder="Select Month"
                                v-model:value="verifiedMonthly.month">
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

                        <a-form-item :validate-status="verifiedMonthly.errors.year ? 'error' : ''"
                            :help="verifiedMonthly.errors.year">
                            <div>
                                Year:

                            </div>
                            <a-select style="width: 30%;" placeholder="Select Year"
                                v-model:value="verifiedMonthly.year">
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
                        <BankOutlined />
                        Verified Store Purchased (Yearly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item :validate-status="verifiedYearly.errors.VerifiedStoreDataTypeYearly ? 'error' : ''"
                            :help="verifiedYearly.errors.VerifiedStoreDataTypeYearly">
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%;" placeholder="Select"
                                v-model:value="verifiedYearly.VerifiedStoreDataTypeYearly">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Store Sales</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item :validate-status="verifiedYearly.errors.selectedStore ? 'error' : ''"
                            :help="verifiedYearly.errors.selectedStore">
                            <div>
                                Store:

                            </div>
                            <a-select v-model:value="verifiedYearly.selectedStore" style="width: 30%;"
                                placeholder="Select Store">
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Alturas Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Alturas Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Island City Mall">Island City
                                    Mall</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Plaza Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Alturas Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Colonade Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Colonade Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Alta Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Farmers Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Ubay Distribution Center">
                                    Ubay Distribution Center</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Screenville">Screenville</a-select-option>
                                <a-select-option v-if="verifiedYearly.VerifiedStoreDataTypeYearly === 'verifiedgc'"
                                    value="Asc Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="verifiedYearly.errors.year ? 'error' : ''"
                            :help="verifiedYearly.errors.year">
                            <div>
                                Year:
                            </div>
                            <a-select style="width: 30%;" placeholder="Select Year" v-model:value="verifiedYearly.year">
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
            verifiedMonthly: {
                VerifiedStoreDataType: '',
                selectedStore: '',
                year: '',
                month: '',
                errors: {}
            },
            verifiedYearly: {
                VerifiedStoreDataTypeYearly: '',
                selectedStore: '',
                year: '',
                errors: {}

            }

        }
    },
    methods: {
        monthlySubmitButton() {
            this.verifiedMonthly.errors = {};
            const { VerifiedStoreDataType, selectedStore, year, month } = this.verifiedMonthly;

            if (!VerifiedStoreDataType) this.verifiedMonthly.errors.VerifiedStoreDataType = "Data type field is required";
            if (!selectedStore) this.verifiedMonthly.errors.selectedStore = "Store field is required";
            if (!year) this.verifiedMonthly.errors.year = "Year field is required";
            if (!month) this.verifiedMonthly.errors.month = "Month field is required";

            if (this.verifiedMonthly.errors.VerifiedStoreDataType || this.verifiedMonthly.errors.selectedStore
                || this.verifiedMonthly.errors.year || this.verifiedMonthly.errors.month) {
                return;
            };
            // if (Object.keys(this.verifiedMonthly.errors).length > 0) {
            //     return;
            // }

            const monthlyData = {
                VerifiedStoreDataType: VerifiedStoreDataType,
                selectedStore: selectedStore,
                year: year,
                month: month
            }
            console.log(monthlyData);
            this.$inertia.get(route('storeaccounting.puchasedMonthlySubmit'), monthlyData)
        },
        yearlySubmitButton() {
            this.verifiedYearly.errors = {};

            const { VerifiedStoreDataTypeYearly, selectedStore, year } = this.verifiedYearly;
            if (!VerifiedStoreDataTypeYearly) this.verifiedYearly.errors.VerifiedStoreDataTypeYearly = "Data type field is required";
            if (!selectedStore) this.verifiedYearly.errors.selectedStore = "Store field is required";
            if (!year) this.verifiedYearly.errors.year = "Year field is required";

            if (this.verifiedYearly.errors.VerifiedStoreDataTypeYearly
                || this.verifiedYearly.errors.selectedStore || this.verifiedYearly.errors.year) {
                return;
            };

            const yearlyData = {
                VerifiedStoreDataTypeYearly: VerifiedStoreDataTypeYearly,
                selectedStore: selectedStore,
                year: year
            }
            console.log(yearlyData);
            this.$inertia.get(route('storeaccounting.purchasedYearlySubmit'), yearlyData);
        }
    }
}
</script>
