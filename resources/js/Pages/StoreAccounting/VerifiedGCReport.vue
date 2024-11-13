<template>
    <div>
        <a-tabs style="font-weight: bold;">

            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <VerifiedOutlined />
                        Verified GC Reports (Monthly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item :validate-status="GCDataTypeMonthly.errors.dataTypeMonthly ? 'error' : ''"
                            :help="GCDataTypeMonthly.errors.dataTypeMonthly">
                            <div>
                                Data Type:
                            </div>

                            <a-select style=" width: 30%;" placeholder="Select"
                                v-model:value="GCDataTypeMonthly.dataTypeMonthly">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Verified GC</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item :validate-status="GCDataTypeMonthly.errors.selectedStoreMonthly ? 'error' : ''"
                            :help="GCDataTypeMonthly.errors.selectedStoreMonthly">
                            <div>
                                Store:
                            </div>
                            <a-select v-model:value="GCDataTypeMonthly.selectedStoreMonthly" style="width: 30%;"
                                placeholder="Select Store">
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Alturas Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Alturas Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Island City Mall">Island City
                                    Mall</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Plaza Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Alturas Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Colonade Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Colonade Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Alta Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Farmers Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Ubay Distribution Center">Ubay Distribution Center</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Screenville">Screenville</a-select-option>
                                <a-select-option v-if="GCDataTypeMonthly.dataTypeMonthly === 'verifiedgc'"
                                    value="Asc Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="GCDataTypeMonthly.errors.month ? 'error' : ''"
                            :help="GCDataTypeMonthly.errors.month">
                            <div>
                                Month:

                            </div>
                            <a-select style="width: 30%; " placeholder="Select Month"
                                v-model:value="GCDataTypeMonthly.month">
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

                        <a-form-item :validate-status="GCDataTypeMonthly.errors.yearMonthly ? 'error' : ''"
                            :help="GCDataTypeMonthly.errors.yearMonthly">
                            <div>
                                Year:

                            </div>

                            <a-select style="width: 30%; " placeholder="Select Year"
                                v-model:value="GCDataTypeMonthly.yearMonthly">
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
                    <a-button @click="submitGCReportsMonthly" style="background-color: #1e90ff; color:white">
                        <SendOutlined /> Submit
                    </a-button>
                </a-card>

            </a-tab-pane>

            <a-tab-pane key="2">
                <template #tab>
                    <span>
                        <VerifiedOutlined />
                        Verified GC Reports (Yearly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item :validate-status="GCDataTypeYearly.errors.GCDataType ? 'error' : ''"
                            :help="GCDataTypeYearly.errors.GCDataType">
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%; " placeholder="Select"
                                v-model:value="GCDataTypeYearly.GCDataType">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Verified GC</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item :validate-status="GCDataTypeYearly.errors.selectedStore ? 'error' : ''"
                            :help="GCDataTypeYearly.errors.selectedStore">
                            <div>
                                Store:

                            </div>
                            <a-select v-model:value="GCDataTypeYearly.selectedStore" style="width: 30%; "
                                placeholder="Select Store">
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Alturas Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Alturas Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Island City Mall">Island
                                    City
                                    Mall</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Plaza Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Alturas Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Colonade Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Colonade Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Alta Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Farmers Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Ubay Distribution Center">Ubay
                                    Distribution Center</a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Screenville">Screenville
                                </a-select-option>
                                <a-select-option v-if="GCDataTypeYearly.GCDataType === 'verifiedgc'"
                                    value="Asc Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item :validate-status="GCDataTypeYearly.errors.year ? 'error' : ''"
                            :help="GCDataTypeYearly.errors.year">
                            <div>
                                Year:
                            </div>
                            <a-select style="width: 30%; " placeholder="Select Year"
                                v-model:value="GCDataTypeYearly.year">
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
                    <a-button @click="submitGCReportsYearly" style="background-color: #1e90ff; color:white">
                        <SendOutlined /> Submit
                    </a-button>
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
            GCDataTypeMonthly: {
                dataTypeMonthly: '',
                yearMonthly: '',
                month: '',
                selectedStoreMonthly: '',
                errors: {}


            },
            GCDataTypeYearly: {
                GCDataType: '',
                selectedStore: '',
                year: '',
                errors: {}
            },
        }
    },
    methods: {
        submitGCReportsMonthly() {
            // alert(1)
            this.GCDataTypeMonthly.errors = {};
            const { dataTypeMonthly, yearMonthly, month, selectedStoreMonthly } = this.GCDataTypeMonthly;
            if (!dataTypeMonthly) this.GCDataTypeMonthly.errors.dataTypeMonthly = " Data type field is required";
            if (!selectedStoreMonthly) this.GCDataTypeMonthly.errors.selectedStoreMonthly = "Store field is required";
            if (!yearMonthly) this.GCDataTypeMonthly.errors.yearMonthly = "Year field is required";
            if (!month) this.GCDataTypeMonthly.errors.month = "Month field is required";

            if (this.GCDataTypeMonthly.errors.dataTypeMonthly || this.GCDataTypeMonthly.errors.yearMonthly || this.GCDataTypeMonthly.errors.month || this.GCDataTypeMonthly.errors.selectedStoreMonthly) {
                return;
            };
            const monthlyData = {
                dataTypeMonthly: dataTypeMonthly,
                yearMonthly: yearMonthly,
                month: month,
                selectedStoreMonthly: selectedStoreMonthly
            }
            console.log(monthlyData);
            this.$inertia.get(route('storeaccounting.verifiedGcSubmit'), monthlyData);
        },
        submitGCReportsYearly() {
            this.GCDataTypeYearly.errors = {};
            const { GCDataType, selectedStore, year } = this.GCDataTypeYearly;

            if (!GCDataType) this.GCDataTypeYearly.errors.GCDataType = "Data Type field is required";
            if (!selectedStore) this.GCDataTypeYearly.errors.selectedStore = "Store field is required";
            if (!year) this.GCDataTypeYearly.errors.year = "Year field is required";

            if (this.GCDataTypeYearly.errors.GCDataType || this.GCDataTypeYearly.errors.selectedStore || this.GCDataTypeYearly.errors.year) {
                return;  
            };
            
            const yearlyData = {
                GCDataType: GCDataType,
                selectedStore: selectedStore,
                year: year
            }
            console.log(yearlyData);
            this.$inertia.get(route('storeaccounting.verifiedGcYearlySubmit'), yearlyData);
        }

    }
}
</script>
