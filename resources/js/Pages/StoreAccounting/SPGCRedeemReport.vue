<template>
    <div>
        <a-tabs v-model:activeKey="activeKey" style="font-weight: bold;">

            <a-tab-pane key="1">
                <template #tab>
                    <span>
                        <PieChartOutlined />
                        SPGC Redeem (Monthly)
                    </span>
                </template>
                <a-card>
                    <div style="margin-top: 20px;">
                        <a-form-item>
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%; border: 1px solid #1e90ff; background-color: #1e90ff"
                                placeholder="Select" v-model:value="SPGCDataType">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Store Sales</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item>
                            <div>
                                Store:
                            </div>
                            <a-select v-model="selectedStore"
                                style="width: 30%; border: 1px solid #1e90ff; background-color: #1e90ff"
                                placeholder="Select Store">
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Alturas
                            Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Alturas
                            Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Island City
                            Mall">Island City
                                    Mall</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Plaza
                            Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Alturas
                                Tubigon">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Colonade
                                Colon">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Colonade
                                Mandaue">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Alta
                                Citta">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Farmers
                                Market">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Ubay
                                Distribution
                                Center">Ubay Distribution Center</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'"
                                    value="Screenville">Screenville</a-select-option>
                                <a-select-option v-if="SPGCDataType === 'verifiedgc'" value="Asc
                                Tech">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item>
                            <div>
                                Month:

                            </div>
                            <a-select style="width: 30%; border: 1px solid #1e90ff; background-color: #1e90ff"
                                placeholder="Select Month">
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

                        <a-form-item>
                            <div>
                                Year:

                            </div>
                            <a-select style="width: 30%; border: 1px solid #1e90ff; background-color: #1e90ff"
                                placeholder="Select Year">
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
                        <a-button style="background-color:#1e90ff; color:white">
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
                        <a-form-item>
                            <div>
                                Data Type:
                            </div>

                            <a-select style="width: 30%; border: 1px solid #1e90ff; background-color: #1e90ff"
                                placeholder="Select" v-model:value="SPGCDataTypeYearly">
                                <a-select-option value="">---Select---</a-select-option>
                                <a-select-option value="verifiedgc">Store Sales</a-select-option>
                            </a-select>

                        </a-form-item>

                        <a-form-item>
                            <div>
                                Store:

                            </div>
                            <a-select v-model="selectedStore"
                                style="width: 30%; border: 1px solid #1e90ff; background-color: #1e90ff"
                                placeholder="Select Store">
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="Alturas
                            Mall">Alturas
                                    Mall</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="Alturas
                            Talibon">Alturas
                                    Talibon</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="Island City
                            Mall">Island City
                                    Mall</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="Plaza
                            Marcela">Plaza
                                    Marcela</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="store5">Alturas
                                    Tubigon</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="store6">Colonade
                                    Colon</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="store7">Colonade
                                    Mandaue</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="store8">Alta
                                    Citta</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="store9">Farmers
                                    Market</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="store10">Ubay
                                    Distribution
                                    Center</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'"
                                    value="store11">Screenville</a-select-option>
                                <a-select-option v-if="SPGCDataTypeYearly === 'verifiedgc'" value="store12">Asc
                                    Tech</a-select-option>
                            </a-select>
                        </a-form-item>

                        <a-form-item>
                            <div>
                                Year:
                            </div>
                            <a-select style="width: 30%; border: 1px solid #1e90ff; background-color: #1e90ff"
                                placeholder="Select Year">
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
                        <a-button style="background-color: #1e90ff; color:white">
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
            SPGCDataType: '',
            SPGCDataTypeYearly: ''
        }
    }
}
</script>
