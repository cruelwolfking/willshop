<template>
  <div>
    <wv-group title="收货地址信息">
      <wv-input label="收货人" v-model="address.name"></wv-input>
      <wv-input label="手机号码" v-model="address.mobile"></wv-input>
      <wv-cell title="所在地区" v-model="address.address" is-link @click="addressPickerShow = true"></wv-cell>
      <wv-input label="街道地址" v-model="address.address"></wv-input>
      <wv-input label="邮政编码" v-model="address.postcode"></wv-input>
    </wv-group>

    <wv-picker v-model="addressPickerShow" :slots="addressSlots" @change="onAddressChange"></wv-picker>

    <footer>
      <wv-flex :gutter="20">
        <wv-flex-item v-if="$route.params.id">
          <wv-button type="warn" @click.native="deleteAddress">删除</wv-button>
        </wv-flex-item>
        <wv-flex-item>
          <wv-button type="primary" @click.native="store">保存</wv-button>
        </wv-flex-item>
      </wv-flex>
    </footer>
  </div>
</template>

<script>
  import WeVue from 'we-vue';
  import chinaAreaData from 'china-area-data'

  let provinces = Object.values(chinaAreaData[86])

  // 获取某一省下的市
  function getCities (province) {
    let provinceCode
    for (let i in chinaAreaData[86]) {
      if (province === chinaAreaData[86][i]) {
        provinceCode = i
        break
      }
    }

    return Object.values(chinaAreaData[provinceCode])
  }

  // 获取某一市下的区/县
  function getAreas (province, city) {
    let provinceCode, cityCode
    for (let i in chinaAreaData[86]) {
      if (province === chinaAreaData[86][i]) {
        provinceCode = i
        break
      }
    }

    for (let i in chinaAreaData[provinceCode]) {
      if (city === chinaAreaData[provinceCode][i]) {
        cityCode = i
        break
      }
    }

    if (chinaAreaData[cityCode]) {
      return Object.values(chinaAreaData[cityCode])
    } else {
      // 只有两级的情况，地区列表直接返回市名
      return [city]
    }
  }

  export default {
    mounted () {
      this.getAddress()
    },

    data () {
      return {
        address: {},
        addressPickerShow: false,
        addressSlots: [
          {
            values: provinces
          },
          {
            values: []
          },
          {
            values: []
          }
        ],
        pca: [],
      }
    },

    methods: {
      onAddressChange (picker, value) {
        picker.setSlotValues(1, getCities(value[0]))
        picker.setSlotValues(2, getAreas(value[0], value[1]))
      },

      getAddress () {
        let addressId = this.$route.params.id

        if (addressId) {
          this.axios.get(`address/${addressId}`).then(response => {
            this.address = response.data.address
          }, response => {
            console.log(response.data)
          })
        }
      },

      // 保存
      store () {
        let postData = JSON.parse(JSON.stringify(this.$data))

        let addressId = this.$route.params.id

        if (addressId) {
          postData.id = addressId
        }

        this.axios.post('address/store', postData).then(response => {
          this.$root.success('保存成功')

          setTimeout(() => {
            this.$router.push('/address')
          }, 1000)
        }, response => {
          console.log(response.data)
        })
      },

      // 删除
      deleteAddress () {
        WeVue.Dialog({
            title: '操作提示',
            message: '确定要删除吗？',
            skin: 'ios'
          },
          () => {
            this.axios.delete(`address/${this.address.id}/delete`).then(() => {
              this.$root.success('删除成功')

              setTimeout(() => {
                this.$router.push('/address')
              }, 1000)
            })
          })
      }
    }
  }
</script>

<style scoped lang="scss">
  footer {
    display: block;
    overflow: hidden;
    position: fixed;
    bottom: 0;
    width: 100%;
    z-index: 20;
    background-color: #fff;
    padding: .5rem 1rem;
  }
</style>
