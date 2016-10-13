<?php
namespace Starbug\Payment;
use Starbug\Core\ModelFactoryInterface;
class ResolvePaymentSubscriptionsCommand {
	function __construct(Authnet $authnet, ModelFactoryInterface $models) {
		$this->authnet = $authnet;
		$this->models = $models;
	}
	public function run($argv) {
		$args = [];
		if (!empty($argv[0])) {
			$args["firstSettlementDate"] = $argv[0];
		}
		if (!empty($argv[1])) {
			$args["lastSettlementDate"] = $argv[1];
		}
		$this->authnet->GetSettledBatchListRequest($args);
		if ($this->authnet->success()) {
			//We have settled batches, let's loop through them and retrieve the transactions
			$batches = $this->authnet->response->batchList->batch;
			foreach ($batches as $batch) {
				$bid = $batch->batchId;
				$this->authnet->GetTransactionListRequest(["batchId" => $bid]);
				if ($this->authnet->success()) {
					//We have the transactions for this batch, let's loop through them and check for subscriptions
					$transactions = $this->authnet->response->transactions->transaction;
					foreach ($transactions as $transaction) {
						echo $transaction->transactionStatus."\n";
						if (isset($transaction->subscription)) {
							$sid = $transaction->subscription->id;
							$subscription = $this->models->get("subscriptions")->load(["subscription_id" => $sid]);
							$payment = $this->models->get("payments")->load(["txn_id" => $transaction->transId]);
							if ($subscription && !$payment) {
								$this->authnet->GetTransactionDetailsRequest(["transId" => $transaction->transId]);
								if ($this->authnet->success()) {
									$details = $this->authnet->response->transaction;
									$payment = [
										"amount" => $transaction->settleAmount,
										"response_code" => $details->responseCode,
										"response" => $transaction->asXML(),
										"txn_id" => $transaction->transId,
										"orders_id" => $subscription["orders_id"],
										"created" => $transaction->submitTimeLocal
									];
									$this->models->get("payments")->store($payment);
									$this->models->get("subscriptions")->store(["id" => $subscription["id"], "expiration_date" => strtotime($subscription["expiration_date"] . "+ " . $subscription["interval"] . $subscription["unit"])]);
								}
							}
						}
					}
				}
			}
		}
	}
}
?>
