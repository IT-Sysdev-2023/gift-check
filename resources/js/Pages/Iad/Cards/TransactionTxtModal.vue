<template>
    <a-modal title="Text file Details" style="top: 20px" :width="1800">
        <a-table size="small" :data-source="record" :pagination="false" :columns="columns" bordered>
        </a-table>
    </a-modal>
</template>
<script lang="ts" setup>

interface RecordType {
    seodtt_id: number;
    seodtt_barcode: number;
    seodtt_eod_id: number;
    seodtt_line: string;
    seodtt_creditlimit: string;
    seodtt_credpuramt: string;
    seodtt_addonamt: string;
    seodtt_balance: string;
    seodtt_transno: string;
    seodtt_timetrnx: string;
    seodtt_bu: string;
    seodtt_terminalno: string;
    seodtt_ackslipno: number;
    seodtt_crditpurchaseamt: string;
}

defineProps<{ record: RecordType }>();

const columns = [
  {
    title: 'Textfile Line',
    dataIndex: 'seodtt_line',
    key: 'name',
  },
  {
    title: 'Credit Limit',
    dataIndex: 'seodtt_creditlimit',
    key: 'age',
  },
  {
    title: 'Cred Pur Amt Add-on',
    dataIndex: 'seodtt_credpuramt',
    key: 'address',
  },
  {
    title: 'Add-on Amount',
    dataIndex: 'seodtt_addonamt',
  },
  {
    title: 'Remaining Balance',
    dataIndex: 'seodtt_balance',
  },
  {
    title: 'Transaction',
    dataIndex: 'seodtt_transno',
  },
  {
    title: 'Time of Cred Tranx',
    dataIndex: '',
  },
  {
    title: 'Business Unit',
    dataIndex: 'seodtt_bu',
  },
  {
    title: 'Terminal #',
    dataIndex: 'seodtt_terminalno',
  },
  {
    title: 'Ackslip #',
    dataIndex: 'seodtt_ackslipno',
  },
];

</script>
