<template>
    <div style="font-weight: bold; margin-left: 70%;">
        Search:
        <a-input allow-clear v-model:value="alturasSearch" style="border: 1px solid #1e90ff; width: 60%;" />
    </div>
    <div style="background-color: #b0c4de; padding: 20px; font-weight: bold; margin-top: 15px;">
        GC Barcode #{{ barcodeNumber }} POS Transaction
    </div>
    <div style="background-color: #b0c4de;">
        <a-table :data-source="data.data" :columns="alturasPosTransaction" :pagination="false">

        </a-table>

        <pagination :datarecords="data" class="mt-5" />
    </div>
    <!-- {{ data }} -->
</template>
<script>
// import { defineComponent } from '@vue/composition-api'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
export default {
    components: { Pagination },
    layout: AuthenticatedLayout,
    props: {
        barcodeNumber: Number,
        data: Object
    },
    data() {
        return {
            alturasSearch: '',
            alturasPosTransaction: [
                {
                    title: 'Textfile Line',
                    dataIndex: 'seodtt_line'
                },
                {
                    title: 'Credit Limit',
                    dataIndex: 'seodtt_creditlimit'
                },
                {
                    title: 'Cred. Pur. Amt + Add-on',
                    dataIndex: 'seodtt_credpuramt'
                },
                {
                    title: 'Add-on Amt',
                    dataIndex: 'seodtt_addonamt'
                },
                {
                    title: 'Remaining Balance',
                    dataIndex: 'seodtt_balance'
                },
                {
                    title: 'Transaction #',
                    dataIndex: 'seodtt_transno'
                },
                {
                    title: 'Time of Cred Tranx',
                    dataIndex: 'seodtt_timetrnx'
                },
                {
                    title: 'Bus. Unit',
                    dataIndex: 'seodtt_bu'
                },
                {
                    title: 'Terminal #',
                    dataIndex: 'seodtt_terminalno'
                },
                {
                    title: 'Ackslip #',
                    dataIndex: 'seodtt_ackslipno'
                },

            ]
        }
    },
    watch: {
        alturasSearch(search) {
            console.log(search);
            this.$inertia.get(route('storeaccounting.talibonPosTransaction', this.barcodeNumber), {
                search: search
            }, {
                preserveState: true
            })
        }
    }
}
</script>
