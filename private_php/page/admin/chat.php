<?php

$_SESSION['tag_sel'] = $_SESSION['tag_sel']? : '';
$tag_sel = $_SESSION['tag_sel'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$post = json_decode(file_get_contents("php://input"), true);
	$action = $post['action'];
	switch ($action) {
		case 'addChat':
			$order_code = date('md');
			$pool = array_merge(range(0,9),range('A', 'Z'));
		    for($i=0; $i < 6; $i++) {
		        $order_code .= $pool[mt_rand(0, 35)];
		    }
		    $name = $post['name'];
		    $delivery_type = $post['delivery_type'];
			$delivery_fee = $post['delivery_fee'];
			$discount = $post['discount'];
			$product = $post['product'];
			$expire_time = (new DateTime())->add(new DateInterval('PT'.$post['exp'].'H'))->format('Y-m-d H:i:s');
			$total_price = $delivery_fee - $discount;
			foreach ($product as $subproduct) {
				$total_price += $subproduct['add_price']*$subproduct['add_amount'];
			}
			$payment_detail = [
				'name' => $name,
				'total_price' => $total_price,
				'expire_time' => $expire_time
			];
			
			$db = new Database();
			$result = $db->call('newChat', [
				$order_code,
				$delivery_type,
				$delivery_fee,
				$discount,
				'chat.jpg',
				json_encode($payment_detail)
			]);
			if(!$result['success']){
				die(json_encode(['success'=>false, 'err_code'=>'ERR_DB']));
			}

			foreach ($product as $subproduct) {
				$result = $db->call('newChatProduct', [
					$order_code,
					$subproduct['product_code'],
					$subproduct['add_price'],
					$subproduct['add_amount']
				]);
				if(!$result['success']){
					die(json_encode(['success'=>false, 'err_code'=>'ERR_DB']));
				}
				else if($result['count']>0){
					die(json_encode(['success'=>false, 'err_code'=>'ERR_LIMIT', 'product'=>$result['result'][0]]));
				}
			}

			die(json_encode(['success'=>true, 'order_code'=>$order_code]));
		break;
		case 'updatePayment':
			$order_code = $post['order_code'];
			$payment_detail = json_encode($post['payment_detail']);

			$db = new Database();
			$db->call('updateChat', [
				$order_code,
				'PAID',
				$payment_detail
			]);
			die(json_encode(['success'=>true, 'order_code'=>$order_code]));
		break;
		case 'delChat':
			$order_code = $post['order_code'];

			$db = new Database();
			$db->call('delChat', [$order_code]);
			die(json_encode(['success'=>true, 'order_code'=>$order_code]));
		break;
		case 'sendTrack':
			$db = new Database();
			$result = $db->call('sentOrder', [$post['order_code'], $post['track_no'], $_SESSION['info']['username']]);
			die(json_encode(['success'=>true]));
		break;
	}
	exit;
}

$db = new Database();
$listChat = $db->call('listChat', [])['result'];
$allProduct = $db->call('allProduct', [])['result'];

?>
<!DOCTYPE html>
<html>
<head>
	<title>Chat | Admin</title>
	<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap/dist/css/bootstrap.min.css"/>
	<link type="text/css" rel="stylesheet" href="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.css"/>
</head>
<body>
	<div id="chatApp">
		<div class="m-2">
			<b-btn class="clip" data-clipboard-text="ร้านค้าอยู่นี่ครับ - https://idolm.art">
		    	ร้านค้าอยู่นี่ครับ...
			</b-btn>
			<b-btn class="clip" data-clipboard-text="ได้รับหลักฐานแล้วครับ จะดำเนินการจัดส่งและแจ้งเลขแทร็กใน 48 ชั่วโมงนะครับ">
		    	ได้รับหลักฐาน...
			</b-btn>
		</div>
		<div>
		  	<b-btn variant="link" v-b-modal.modal_add>+เพิ่มลูกค้า</b-btn>
		  	<b-btn variant="link" @click="sendTrack">✓ส่งเลขแทร็กทั้งหมด</b-btn>
		</div>
		<b-table striped hover :items="chat_items" :fields="chat_fields" @row-clicked="rowClick">
			<span slot="expire" slot-scope="data" v-html="data.value"></span>
			<span slot="product" slot-scope="data" v-html="data.value"></span>
			<span slot="is_address" slot-scope="data" v-html="data.value"></span>
		</b-table>
		<div>
		  	<b-modal hide-footer id="modal_add" title="เพิ่มลูกค้า">
		    	<b-form @submit.prevent="submitAdd">
		    		<b-row>
			      		<b-col cols="8">
					      	<b-form-group label="ชื่อ">
					        	<b-form-input type="text" v-model="add_iname" required>
					        	</b-form-input>
					      	</b-form-group>
					    </b-col>
					    <b-col>
					      	<b-form-group label="โอน (ชม.)">
					        	<b-form-input type="number" v-model="add_exp" required></b-form-input>
					      	</b-form-group>
					    </b-col>
					</b-row>
			      	<b-row>
			      		<b-col>
					      	<b-form-group label="วิธีส่ง">
					        	<b-form-select v-model="delivery_selected" :options="delivery_options" class="mb-3">
					      	</b-form-group>
					    </b-col>
					    <b-col>
					      	<b-form-group label="ค่าส่ง">
					        	<b-form-input type="number" v-model="add_ifee" required></b-form-input>
					      	</b-form-group>
					    </b-col>
			      		<b-col>
					      	<b-form-group label="ส่วนลด">
					        	<b-form-input type="number" v-model="add_idiscount" required></b-form-input>
					      	</b-form-group>
			      		</b-col>
			      	</b-row>
			      	<b-table :items="add_product" :fields="add_product_fields">
						<span slot="add_price" slot-scope="data">
							<b-form-input type="number" v-model="add_product[data.index].add_price" step="10" required></b-form-input>
						</span>
						<span slot="add_amount" slot-scope="data">
							<b-form-input type="number" v-model="add_product[data.index].add_amount" required @change="checkProductAmount"></b-form-input>
						</span>
					</b-table>
					<b-alert variant="danger" dismissible :show="is_add_err" @dismissed="is_add_err=false">{{ add_err_msg }}</b-alert>
			      	<b-button type="submit" variant="success">ตกลง</b-button>
			    </b-form>
			    <hr>
			    <b-row>
		      		<b-col>
	    				<b-button variant="primary" @click="addCart">ใส่ตะกร้า</b-button>
	    			</b-col>
	    			<b-col cols="9">
	    				<b-form-input type="text" v-model="add_tag" placeholder="ค้นหา"></b-form-input>
	    			</b-col>
	    		</b-row>
		      	<b-form-select v-model="product_selected" :options="product_options" class="mb-2" :select-size="10">
		  	</b-modal>
		  	<b-modal hide-footer id="modal_edit" v-model="is_edit" :title="edit_title">
		  		<b-btn variant="danger" @click="delChat">ลบ {{ edit_title }}</b-btn>
		  		<hr>
		  		<b-form @submit.prevent="submitEdit">
		  			<b-form-group label="ใส่ที่อยู่">
			      		<b-form-textarea v-model="edit_address.input" placeholder="ชื่อ เบอร์โทร                                                                                                       ที่อยู่ รหัสไปรษณีย์" rows="4" required></b-form-textarea>
			      	</b-form-group>
				    <p>
				    	<strong>กรุณาส่ง </strong>{{ edit_address.name_tel }}<br>
						<strong>ที่อยู่ </strong>{{ edit_address.address }}<br>
				    	<strong>รหัสไปรษณีย์ </strong>{{ edit_address.post }}
				    </p>
			      	<b-button type="submit" variant="success">ตกลง</b-button>
			    </b-form>
		  	</b-modal>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
	<script type="text/javascript">
		new ClipboardJS('.clip')
	</script>
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>
	<script src="https://unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
	<script src="https://unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.min.js"></script>
	<script type="text/javascript">
		var app = new Vue({
		  el: '#chatApp',
		  data: {
		  	add_product_fields: [
		        { key: 'product_code', 	 label: 'สินค้า',   sortable: true},
		        { key: 'price',          label: 'ราคา',   sortable: true},
		        { key: 'add_price',      label: 'ราคา',  sortable: true},
		        { key: 'amount',         label: 'จำนวน',   sortable: true},
		        { key: 'add_amount',     label: 'จำนวน',   sortable: true}
		    ],
		  	chat_fields: [
		        { key: 'name', 	 label: 'ชื่อ',   sortable: true},
		        { key: 'status', label: 'สถานะ',   sortable: true},
		        { key: 'expire', label: 'หมดเวลา',  sortable: true},
		        { key: 'product',label: 'สินค้า',  sortable: true},
		        { key: 'price',  label: 'ยอดรวม',  sortable: true},
		        { key: 'is_address',label: 'ที่อยู่', sortable: true}
		    ],
		  	is_add_err: false,
		  	add_iname: '',
		  	add_exp: 4,
		  	add_idiscount: 0,
		  	add_ifee: 50,
		  	add_product: [],
		  	add_err_msg: '',
		  	add_tag: '<?=$tag_sel?>',
		  	is_edit: false,
		  	edit_order: null,
		  	edit_title: '',
		  	edit_address: {},
		  	edit_payment: {},
		    chat_items: <?=json_encode($listChat?:[])?>.map(x => {
		    	payment_detail = JSON.parse(x.payment_detail)
		    	return {
		    		order_code: x.order_code,
			    	status: ({BILL: 'รอโอน', PAID: 'โอนแล้ว', PACK: 'รอแทร็ก'})[x.status],
			    	stat_val: x.status,
			    	product: x.product,
			    	name: payment_detail.name,
			    	expire: '<span class="text-' + (x.status == 'BILL' && (new Date()) > Date.parse(payment_detail.expire_time) ? 'danger' : 'dark') + '">' + payment_detail.expire_time + '</span>',
			    	price: payment_detail.total_price,
			    	is_address: !!payment_detail.address_raw ? payment_detail.address_raw.name_tel.split(' ')[0]: '<span class="text-danger">ว่าง</span>',
			    	address_raw: payment_detail.address_raw,
			    	payment_detail: payment_detail
			    }
			}),
		    product_selected: null,
		    product_options: <?=json_encode($allProduct)?>.map(x => ({
		    	text: x.product_code,
		    	value: {product_code: x.product_code, amount: x.amount, price: x.price}
		    })),
		    all_product: <?=json_encode($allProduct)?>.map(x => ({
		    	text: x.product_code,
		    	value: {product_code: x.product_code, amount: x.amount, price: x.price}
		    })),
		    delivery_selected: 'EMS',
		    delivery_options: Object.keys(<?=json_encode(GEN::DELIVERY)?>)
		  },
		  methods: {
		  	submitAdd () {
		  		this.$http.post('/api/admin/chat/', {
		  			action: 'addChat',
		  			name: this.add_iname,
		  			exp: this.add_exp,
		  			delivery_type: this.delivery_selected,
		  			delivery_fee: this.add_ifee,
		  			discount: this.add_idiscount,
		  			product: this.add_product
		  		}).then(res => {
			      let that = res.body
			      if(that.success){
			      	location.reload(true)
			      }
			      else{
			      	this.add_err_msg = that.err_code == 'ERR_LIMIT' ? that.product.product_code + ' ไม่เกิน ' + that.product.amount + ' ชิ้น'  : 'ข้อผิดพลาดของระบบ'
			      	this.is_add_err = true
			      }
			    })
		  	},
		  	submitEdit () {
		  		this.edit_payment.address_raw = {
		  			name_tel: this.edit_address.name_tel,
		  			address:  this.edit_address.address,
		  			post:     this.edit_address.post
		  		}
		  		this.$http.post('/api/admin/chat/', {
		  			action: 'updatePayment',
		  			order_code: this.edit_order,
		  			payment_detail: this.edit_payment
		  		}).then(res => {
			      let that = res.body
			      location.reload(true)
			    })
		  	},
		  	addCart() {
		  		if(this.product_selected == null || this.add_product.findIndex(x => x.product_code == this.product_selected.product_code) != -1) return
		  		this.add_product.push({
		  			product_code: this.product_selected.product_code,
		  			price:        this.product_selected.price,
		  			add_price:    this.calPrice(this.product_selected.price),
		  			amount:       this.product_selected.amount,
		  			add_amount: 1
		  		})
		  	},
		  	calPrice (x) {
		  		return parseInt(x)// + (x > 200 ? 20 : 10)
		  	},
		  	checkProductAmount () {
		  		this.add_product = this.add_product.filter(x => x.add_amount > 0)
		  	},
		  	rowClick (item) {
		  		this.is_edit = true
		  		this.edit_title = item.name + ' (' + item.order_code + ')'
		  		this.edit_order = item.order_code
		  		this.edit_address = {input: ''}
		  		this.edit_payment = item.payment_detail
		  		if(item.address_raw != undefined){
			  		this.edit_address.input = item.address_raw.name_tel + "\n" + item.address_raw.address + ' ' + item.address_raw.post
			  	}
		  	},
		  	delChat () {
		  		this.$http.post('/api/admin/chat/', {
		  			action: 'delChat',
		  			order_code: this.edit_order
		  		}).then(res => {
			      let that = res.body
			      location.reload(true)
			    })
		  	},
		  	sendTrack () {
		  		order = this.chat_items.filter(x => x.stat_val == 'PACK').map(x => x.order_code).join(' ')
		  		track = order.split(' ').map(x => 'CHAT').join(' ')
		  		this.$http.post('/api/admin/chat/', {
		  			action: 'sendTrack',
		  			order_code: order,
		  			track_no: track
		  		}).then(res => {
			      let that = res.body
			      location.reload(true)
			    })
		  	}
		  },
		  watch: {
		  	delivery_selected () {
		  		this.add_ifee = (<?=json_encode(GEN::DELIVERY)?>)[this.delivery_selected]
		  	},
		  	'edit_address.input'() {
		  		this.edit_address.name_tel = this.edit_address.input.split("\n")[0]

		  		if(this.edit_address.input.split("\n")[1] == undefined){
		  			this.edit_address.address = null
		  			this.edit_address.post = null
		  			return
		  		}
		  		this.edit_address.address = this.edit_address.input.split("\n").filter((e,i)=>i>0).join(' ').match(/^(.*)(\d{5})[ ]*$/)

		  		if(!this.edit_address.address){
		  			this.edit_address.address = this.edit_address.input.split("\n").filter((e,i)=>i>0).join(' ')
		  		}
		  		else{
		  			this.edit_address.post = this.edit_address.address[2]
		  			this.edit_address.address = this.edit_address.address[1]
		  		}
		  	},
		  	is_edit () {
		  		if(!this.is_edit) {
		  			this.edit_address = {input: ''}
		  		}
		  	},
		  	add_tag: {
		  		immediate: true,
    			handler() {
			  		if(this.add_tag == '') {
			  			this.product_selected = null
			  			this.product_options = this.all_product
			  		}
			  		else {
				  		this.$http.post('/api/vpost/', {
				  			action: 'getSearchInfo',
				  			query: this.add_tag
				  		}).then(res => {
					      let that = res.body
					      this.product_selected = null
					      this.product_options = Object.keys(that.search.result).map(x =>({
					    	text: that.search.result[x].product_code,
					    	value: {product_code: that.search.result[x].product_code, amount: that.search.result[x].amount, price: that.search.result[x].price}
					      }))
					    })
				  	}
				}
		  	}
		  }
		})
	</script>
</body>
</html>