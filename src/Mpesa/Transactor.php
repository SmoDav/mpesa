<?php
namespace SmoDav\Mpesa;

use Carbon\Carbon;
use DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use SmoDav\Mpesa\Contracts\Transactable;
use SmoDav\Mpesa\Exceptions\TransactionException;

/**
 * Class Transactor
 *
 * @category PHP
 * @package  SmoDav\Mpesa
 * @author   David Mjomba <smodavprivate@gmail.com>
 */
class Transactor
{
    /**
     * The hashed password.
     *
     * @var string
     */
    protected $password;

    /**
     * The transaction timestamp.
     *
     * @var int
     */
    protected $timestamp;

    /**
     * The transaction reference id
     *
     * @var int
     */
    protected $referenceId;

    /**
     * The amount to be deducted
     *
     * @var int
     */
    protected $amount;

    /**
     * The Mobile Subscriber number to be billed.
     * Must be in format 2547XXXXXXXX.
     *
     * @var int
     */
    protected $number;

    /**
     * The keys and data to fill in the request body.
     *
     * @var array
     */
    protected $keys;

    /**
     * The request to be sent to the endpoint
     *
     * @var string
     */
    protected $request;

    /**
     * The generated transaction number by the Transactable implementer.
     *
     * @var string
     */
    protected $transactionNumber;

    /**
     * The Guzzle Client used to make the request to the endpoint.
     *
     * @var Client
     */
    private $client;

    /**
     * The M-Pesa configuration repository.
     *
     * @var MpesaRepository
     */
    private $repository;

    /**
     * Transactor constructor.
     *
     * @param MpesaRepository $repository
     */
    public function __construct(MpesaRepository $repository)
    {
        $this->client = new Client([
            'verify'          => false,
            'timeout'         => 60,
            'allow_redirects' => false,
            'expect'          => false,
        ]);

        $this->repository = $repository;
    }

    /**
     * Process the transaction request.
     *
     * @param $amount
     * @param $number
     * @param $referenceId
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function process($amount, $number, $referenceId)
    {
        $this->amount = $amount;
        $this->number = $number;
        $this->referenceId = $referenceId;
        $this->initialize();

        return $this->handle();
    }

    /**
     * Initialize the transaction.
     */
    protected function initialize()
    {
        $this->setTimestamp();
        $this->generatePassword();
        $this->setupKeys();
    }

    /**
     * Validate and execute the transaction.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function handle()
    {
        $this->validateKeys();
        $this->generateRequest('request.xml');
        $this->send();
        $this->generateRequest('process.xml');

        return $this->send();
    }

    /**
     * Set the transaction timestamp.
     */
    private function setTimestamp()
    {
        if ($this->repository->demo) {
            $this->timestamp = '20160510161908';

            return $this->timestamp;
        }
        $this->timestamp = Carbon::now()->format('YmdHis');

        return $this->timestamp;
    }

    /**
     * Override the config pay bill number and pass key.
     *
     * @param $payBillNumber
     * @param $payBillPassKey
     *
     * @return $this
     */
    public function setPayBill($payBillNumber, $payBillPassKey)
    {
        $this->repository->paybillNumber = $payBillNumber;
        $this->repository->passkey = $payBillPassKey;

        return $this;
    }

    /**
     * Generate the password for the transaction.
     */
    private function generatePassword()
    {
        if ($this->repository->demo) {
            $this->password = 'ZmRmZDYwYzIzZDQxZDc5ODYwMTIzYjUxNzNkZDMwMDRjNGRkZTY2ZDQ3ZTI0YjVjODc4ZTExNTNjMDA1YTcwNw==';

            return $this->password;
        }

        $passwordSource = $this->repository->paybillNumber . $this->repository->passkey . $this->timestamp;
        $this->password = base64_encode(hash("sha256", $passwordSource));

        return $this->password;
    }

    /**
     * Map the document fields with the transaction details.
     */
    protected function setupKeys()
    {
        $this->keys = [
            'VA_PAYBILL'     => $this->repository->paybillNumber,
            'VA_PASSWORD'    => $this->password,
            'VA_TIMESTAMP'   => $this->timestamp,
            'VA_TRANS_ID'    => $this->getTransactionNumber(),
            'VA_REF_ID'      => $this->referenceId,
            'VA_AMOUNT'      => $this->amount,
            'VA_NUMBER'      => $this->number,
            'VA_CALL_URL'    => $this->repository->callbackUrl,
            'VA_CALL_METHOD' => $this->repository->callbackMethod,
        ];
    }

    /**
     * Get the transaction number from the Transactible implementer.
     *
     * @return string
     * @throws \Exception
     */
    private function getTransactionNumber()
    {
        $handler = $this->repository->transactionGenerator;

        if (! new $handler instanceof Transactable) {
            throw new \Exception('Generator Handler does not implement the Transactible Interface.');
        }

        $this->transactionNumber = $handler::generateTransactionNumber();

        return $this->transactionNumber;
    }

    /**
     * Validate the required fields.
     */
    private function validateKeys()
    {
        Validator::validate($this->keys);
    }

    /**
     * Fetch the XML document and include the transaction data.
     *
     * @param string $document
     */
    private function generateRequest($document)
    {
        $this->request = file_get_contents(__DIR__ . '/soap/' . $document);

        foreach ($this->keys as $key => $value) {
            $this->request = str_replace($key, $value, $this->request);
        }
    }

    /**
     * Execute the request.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function send()
    {
        $response = $this->client->request('POST', $this->repository->endpoint, [
                'body' => $this->request
            ]);

        $this->validateResponse($response);

        return $response;
    }

    /**
     * Validate the response is a success, throw error if not.
     *
     * @param Response $response
     *
     * @throws TransactionException
     */
    private function validateResponse($response)
    {
        $message = $response->getBody()->getContents();
        $response->getBody()->rewind();
        $doc = new DOMDocument();
        $doc->loadXML($message);

        $responseCode = $doc->getElementsByTagName('RETURN_CODE')->item(0)->nodeValue;
        if ($responseCode != '00') {
            $responseDescription = $doc
                ->getElementsByTagName('DESCRIPTION')
                ->item(0)
                ->nodeValue;

            throw new TransactionException('Failure - ' . $responseDescription);
        }
    }
}
