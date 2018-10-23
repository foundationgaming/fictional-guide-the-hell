<?php

namespace quotemaker\controllers;

use Mpdf\Mpdf;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Silex\Application;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use quotemaker\domain\Customer;
use quotemaker\domain\Quote;
use quotemaker\domain\QuoteLine;
use quotemaker\domain\QuoteLineColorbond;
use quotemaker\domain\QuoteLineDimension;
use quotemaker\domain\QuoteLineNote;
use quotemaker\domain\QuoteLineQuantity;
use quotemaker\myob\MyobOauth;
use quotemaker\services\QuotemakerService;

class MainController
{
    protected $twig;
    protected $dao;
    /**
     * @var QuotemakerService
     */
    protected $service;

    function __construct($twig, $service)
    {
        $this->twig = $twig;
        $this->service = $service;
    }

    public function index(Request $request, Application $app)
    {
        $form = $app ['form.factory']->create ( "quotemaker\\forms\CustomerSearchFormType", NULL );
        $form->handleRequest( $request );
        $data = array();

        if ($form->isSubmitted() && $form->isValid()) {
            $quoteNumber = $form->getData()['quoteId'];
            $customerName = $form->getData()['customerName'];
            // check if the quote actually exists
            if ($quoteNumber > 0) {
                $quote = $this->service->getQuoteByInvoiceNumber($quoteNumber);
                if ($quote) {
                    return $app->redirect( "/newquote5/{$quote->getId()}" );
                } else {
                    return $this->twig->render('index.html.twig', array(
                        'form' => $form->createView(),
                        'data' => $data,
                        'noResults' => false,
                        'noQuotes' => true
                    ));
                }
            } else {
                $noResults = true;
                if (!empty($customerName)) {
                    $data = $this->service->searchCustomers($form->getData() ['customerName'], $app['services.myobAPI']);
                    $noResults = (count($data) == 0) ? true : false;
                }
                
                return $this->twig->render('index.html.twig', array(
                    'form' => $form->createView(),
                    'data' => $data,
                    'noResults' => $noResults,
                    'noQuotes' => false));
            }
        } else {
            return $this->twig->render( 'index.html.twig', array (
                'form' => $form->createView(),
                'data' => $data,
                'noResults' => false,
                'noQuotes' => false
            ));
        }
    }

    public function customerDetails(Request $request, Application $app, $customerId)
    {
        $customer = $app['services.myobAPI']->getCustomerById ( $customerId );        
        // at this point, insert the customer into our local database?
        
        $customerObj = new Customer();
        $customerObj->populateFromJson($customer["Items"][0]);
        $this->service->insertOrUpdateCustomer($customerObj);
        
        $quotes = $this->service->getQuotesForCustomer ( $customerId );
        return $this->twig->render ( 'customer-details.html.twig', array (
                "customer" => $customerObj,
                "quotes" => $quotes
        ) );
    }

    public function newQuote(Request $request, Application $app, $customerId = null)
    {
        $customer = null;
        if ($customerId != null) {
            $customer = $this->service->getCustomerById ( $customerId );
        }

        $fenceTypes = $this->service->getAllFenceTypes();
        return $this->twig->render ( 'new-quote.html.twig', array (
                'customer' => $customer,
                'fenceTypes' => $fenceTypes
        ) );
    }

    public function newQuote4(Request $request, Application $app)
    {
        $panelId = $request->request->get ( 'panelId' );
        $quoteId = $request->request->get ( 'quoteId' );
        $quoteItem = $request->request->get ( 'quoteItem' );
        $quoteItemText = $request->request->get ( 'quoteItemText' );
        $quoteItemNotes = $request->request->get ( 'notes' );

        // Make a quote line for this
        $dbQuoteLine = new QuoteLine ();
        $dbQuoteLine->setCost ( 0 ); // TODO - calculate cost
        $dbQuoteLine->setItemType ( $quoteItem );
        $dbQuoteLine->setLength ( $quoteItemText );
        $dbQuoteLine->setId ( $quoteId );
        $dbQuoteLine->setNotes ( $quoteItemNotes );

        if ($quoteItem == "P") {
            $dbQuoteLine->setPanelId ( $panelId );
            $dbQuoteLine->setNumberOfPanels ( $this->service->getNumberOfPanelsForLength ( $dbQuoteLine->getLength (), $panelId ) );
        }

        $this->service->insertQuoteLine ($dbQuoteLine, $this->getUsername($app));
        $app ['session']->getFlashBag()->add ( 'lastPanelType', $panelId );

        return $app->redirect ( "/showQuote/{$quoteId}" );
    }

    public function newQuote5(Request $request, Application $app, $id)
    {
                
        $quote = $this->service->getQuoteById ( $id );        
        $customer = $this->service->getCustomerById($quote->getCustomerId());
        $quote = $this->service->getQuoteById ( $id );
        $quoteLines = $this->service->getQuoteLinesByQuoteId ( $id );
        $quote->setQuoteLines ( $quoteLines );
        $emailHistory = $this->service->quoteVersionHistory( $id );
                
        return $this->twig->render ( 'newquote/new-quote-5.html.twig', array (
            'quote' => $quote,
            'customer' => $customer,
            'emailHistory' => $emailHistory)
        );
    }

    public function deleteQuoteLine(Request $request, Application $app, $id)
    {
        $quoteLine = $this->service->getQuoteLineById ( $id );
        $quoteId = $quoteLine->getQuoteId ();
        $this->service->deleteQuoteLine($id, $this->getUsername($app));
        return $app->redirect ( "/showQuote/{$quoteId}" );
    }

    public function deleteQuote(Request $request, Application $app, $id)
    {
        $quote = $this->service->getQuoteById( $id );
        $this->service->deleteQuote( $id );
        return $app->redirect ( "/customerDetails/{$quote->getCustomerId()}" );
    }

    public function adjustQuote(Request $request, Application $app, $id)
    {
        $data = $this->service->getAdjustment($id);
        $form = $app ['form.factory']->create ( "quotemaker\\forms\AdjustQuoteFormType", $data );
        $form->handleRequest ( $request );

        if ($form->isSubmitted () && $form->isValid ()) {
            $data = $form->getData();
            $this->service->adjustQuote($id, $data->getPercentage(), $this->getUsername($app));
            return $app->redirect ( "/newquote5/{$id}" );
        } else {
            return $this->twig->render ( 'adjust-quote.html.twig', array (
                'form' => $form->createView ()
            ) );
        }
    }

    public function showQuote(Request $request, Application $app, $id)
    {
        $quote = $this->service->getQuoteById ( $id );
        $notes = $this->service->getAllNotes();
        $items = $this->service->getAllItems();

        return $this->twig->render ( 'showquote.html.twig', array (
                'quote' => $quote,
                'notes' => $notes,
                'items' => $items
        ) );
    }
        
    public function printQuote(Request $request, Application $app, $id, $revision=NULL)
    {   
        $rootDir = getcwd();        
        $this->generatePdf($id, $app);
        $myobInvoiceNumber = $this->service->getMyobInvoiceNumberForQuote($id, $revision);
        return new BinaryFileResponse("$rootDir/../quotes/A-{$myobInvoiceNumber}.pdf");
    }
    
    private function saveQuoteToMyob($id, $app) 
    {
        $quote = $this->service->getQuoteById ( $id );
        if ($quote->getMyobInvoiceNumber() == null) {
            $myobService = $app['services.myobAPI'];
            $returnVal = $myobService->addQuote($quote);
            $myobService->updateQuoteData($returnVal, $quote);                                       
        }
    }

    private function generatePdf($id, $app)
    {
        $quote = $this->service->getQuoteById ( $id );
        $rootDir = getcwd();
        $invoiceNumber = $quote->getMyobInvoiceNumber();
        $filename = "$rootDir/../quotes/A-$invoiceNumber.pdf";
        if (!file_exists($filename || $invoiceNumber == NULL)) {            
            $this->saveQuoteToMyob($id, $app);
            // now we need to update the quote again, because its data has changed
            $quote = $this->service->getQuoteById( $id );
            $quoteLines = $this->service->getQuoteLinesByQuoteId ( $id );
            $quote->setQuoteLines ( $quoteLines );
            
            // we also need to update the pdf filename, now we know the invoice number 
            $invoiceNumber = $quote->getMyobInvoiceNumber();
            $filename = "$rootDir/../quotes/A-$invoiceNumber.pdf";
            $mpdf = new Mpdf();
            $mpdf->SetImportUse();
            $mpdf->setTitle("Quote A-$invoiceNumber");

            // Add First page
            $mpdf->AddPage();
            $templatePath = getcwd() . "../../templates/quote-template-6.pdf";
            $pagecount = $mpdf->SetSourceFile($templatePath);
            $tplId = $mpdf->ImportPage($pagecount);
            $mpdf->UseTemplate($tplId);
            $todayDate = time(); //date("d/m/Y"); // TODO: GET THIS OFF THE QUOTE OBJECT
            $customer = $this->service->getCustomerById($quote->getCustomerId());

            $htmlFrag = $this->twig->render( 'quote-pdf.html.twig',
                array('quote' => $quote, 'customer' => $customer, 'todayDate' => $todayDate));

            $mpdf->WriteHTML($htmlFrag);
            $mpdf->Output($filename, \Mpdf\Output\Destination::FILE);
            $this->service->pdfCreated($id, $quote->getQuoteVersion());
        }
    }

    public function emailQuote(Request $request, Application $app, $id)
    {
        $this->generatePdf($id, $app);
        $userInfo = $this->service->getUserByUsername($this->getUsername($app));
        $quote = $this->service->getQuoteById ( $id );
        $customer = $this->service->getCustomerById($quote->getCustomerId());
        $bodyText = $this->service->getEmailBodyText();
        $footerText = $this->service->getEmailFooterText();
        $bodyText = str_replace("{FIRST_NAME}", $customer->getFirstName(), $bodyText);
        $footerText = str_replace("{SENDER_NAME}", $userInfo->getRealName(), $footerText);

        return $this->twig->render ('emailquote.html.twig', array (
            'quote' => $quote,
            'customer' => $customer,
            'bodyText' => $bodyText,
            'footerText' => $footerText
        ));
    }

    public function sendQuoteByEmail(Request $request, Application $app)
    {
        $toEmail = $request->request->get ( 'toEmail' );
        $ccEmail = $request->request->get ( 'ccEmail' );
        $subject = $request->request->get ( 'subject' );
        $body = $request->request->get ( 'body' );
        $quoteId = $request->request->get('quoteId' );
        $quote = $this->service->getQuoteById($quoteId);

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {

            //Server settings
            $mail->SMTPDebug = 1;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = getenv('mail_host');  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = getenv('mail_username');                 // SMTP username
            $mail->Password = getenv('mail_password');                           // SMTP password
            if (getenv("mail_tls") != NULL) {
                $mail_tls = (strtolower(getenv("mail_tls")=="true"));
            } else {
                $mail_tls = true;
            }
            if ($mail_tls) {
                $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            }
            $mail->Port = getenv('mail_port');                                    // TCP port to connect to
            $mailFrom = getenv('mail_from');

            //Recipients
            $mail->setFrom($mailFrom, "Country Custom Balustrade + Fencing");
            $mail->addAddress($toEmail);
            if ($ccEmail !== '') {
                $mail->addCC($ccEmail);
            }

            //Attachments
            $this->generatePdf($quoteId, $app);
            $rootDir = getcwd();
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT']. "/assets/footer-logo.jpg", 'logoimage');
            $mail->AddEmbeddedImage($_SERVER['DOCUMENT_ROOT']. "/assets/footer-social.jpg", 'socialimg');
            $mail->addAttachment("$rootDir/../quotes/A-{$quote->getMyobInvoiceNumber()}.pdf");
            $body = str_replace("../assets/footer-logo.jpg", "cid:logoimage", $body);
            $body = str_replace("../assets/footer-social.jpg", "cid:socialimg", $body);
            $mail->msgHTML($body);
            $mail->send();
            $this->service->emailSent($quoteId, $quote->getQuoteVersion());
            return $app->redirect ( "/newquote5/{$quoteId}" );
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return 'Message could not be sent';
        }
    }

    public function addLineToQuote(Request $request, Application $app)
    {
        $quoteId = $request->query->get ( 'quoteId' );
        $itemId = $request->query->get ( 'quoteItem' );
        $theItem = $this->service->getItemById( $itemId );

        if ($theItem->getType() == 'N') {
            $quoteLine = new QuoteLineNote();
            $quoteLine->setQuoteId($quoteId);
            $quoteLine->setItemId($itemId);
            $quoteLine->setItemType($theItem->getType());
            $quoteLine->setService($this->service);
            $form = $app['form.factory']->create("quotemaker\\forms\\QuoteLineNoteFormType", $quoteLine);
        } else if ($theItem->getType() == 'SG' || $theItem->getType() == 'DG') {
            $quoteLine = new QuoteLineDimension($app['services.gateService']);
            $quoteLine->setQuoteId($quoteId);
            $quoteLine->setItemId($itemId);
            $quoteLine->setItemType($theItem->getType());
            $quoteLine->setService($this->service);
            $quoteDefaults = $this->service->getDefaultQuoteSettings ($quoteId);
            $quoteLine->setDefaults($quoteDefaults);
            if ($theItem->getType() == 'SG') {
                $quoteLine->setLength(900);
            } else {
                $quoteLine->setLength(3000);
            }
            $form = $app['form.factory']->create("quotemaker\\forms\\QuoteLineColorbondGateFormType", $quoteLine);
        } else if ($theItem->getType() == 'C') {
            // colorbond
            $quoteLine = new QuoteLineColorbond();
            $quoteLine->setQuoteId($quoteId);
            $quoteLine->setItemId($itemId);
            $quoteLine->setItemType($theItem->getType());
            $quoteLine->setService($this->service);
            $quoteDefaults = $this->service->getDefaultQuoteSettings ($quoteId);
            $quoteLine->setDefaults($quoteDefaults);
            $form = $app['form.factory']->create("quotemaker\\forms\\QuoteLineColorbondFormType", $quoteLine);
        } else if ($theItem->getType() == 'PT') {
            $quoteLine = new QuoteLineNote();
            $quoteLine->setQuoteId($quoteId);
            $quoteLine->setItemId($itemId);
            $quoteLine->setItemType($theItem->getType());
            
            // add straight to quote
            $this->service->addQuoteLineToQuote($quoteLine, $this->getUsername($app));
            return $app->redirect ( "/showQuote/{$quoteId}" );            
        } else {
            $quoteLine = new QuoteLineQuantity();
            $quoteLine->setQuoteId($quoteId);
            $quoteLine->setItemId($itemId);
            $quoteLine->setItemType($theItem->getType());
            $quoteLine->setService($this->service);
            $form = $app['form.factory']->create("quotemaker\\forms\\QuoteLineQuantityFormType", $quoteLine);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $this->service->addQuoteLineToQuote($item, $this->getUsername($app));
            return $app->redirect ( "/showQuote/{$quoteId}" );
        }
        if ($theItem->getType() == 'C') {
            return $this->twig->render('newquote/colourbond.html.twig', array(
                    'form' => $form->createView(),
                    'title' => $theItem->getDescription()
            ));
        } else {
            return $this->twig->render('newquote/add-line-to-quote.html.twig', array(
                'form' => $form->createView(),
                'title' => $theItem->getDescription()
            ));
        }
    }

    public function editQuoteLine(Request $request, Application $app, $id)
    {
        $theLine = $this->service->getQuoteLineById( $id );
        $theLine->setService($this->service);
        $theLine->populate();
        $quoteId = $theLine->getQuoteId();
        $theItem = $this->service->getItemById($theLine->getItemId());

        if ($theLine->getItemType() == 'N') {
            $form = $app['form.factory']->create("quotemaker\\forms\\QuoteLineNoteFormType", $theLine);
        } else if ($theLine->getItemType() == 'SG' || $theLine->getItemType() == 'DG') {
            $form = $app['form.factory']->create("quotemaker\\forms\\QuoteLineColorbondGateFormType", $theLine);
        } else if ($theLine->getItemType() == 'C') {
            $form = $app['form.factory']->create("quotemaker\\forms\\QuoteLineColorbondFormType", $theLine);
        } else {
            $form = $app['form.factory']->create("quotemaker\\forms\\QuoteLineQuantityFormType", $theLine);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $theLine = $form->getData();
            $this->service->editQuoteLine($theLine, $this->getUsername($app));
            return $app->redirect ( "/showQuote/{$quoteId}" );
        }

        if ($theLine->getItemType() == 'C') {
            return $this->twig->render('newquote/colourbond.html.twig', array(
                    'form' => $form->createView(),
                    'title' => $theItem->getDescription()
            ));
        } else {
            return $this->twig->render('newquote/add-line-to-quote.html.twig', array(
                    'form' => $form->createView(),
                    'title' => $theItem->getDescription()
            ));
        }
    }

    public function newCustomer(Request $request, Application $app)
    {
                
        $form = $app['form.factory']->create("quotemaker\\forms\\CustomerFormType", null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            $customer = $form->getData();
            $returnVal = $app['services.myobAPI']->addCustomer($customer);                                                           
            $data = explode("\n",$returnVal);
            $customerId = NULL;
            foreach($data as $part){                
                if (substr($part, 0, 9) == "Location:") {
                    $pos = strrpos($part, '/');
                    $customerId = $pos === false ? $part :  str_replace(array("\r", "\n"), '', substr($part, $pos + 1)); // removes any extraneous chars 
                    break;
                }
            }
            $customer->setId($customerId);
            $this->service->insertOrUpdateCustomer($customer);
            return $app->redirect("/customerDetails/{$customerId}");
        }
        return $this->twig->render('admin/edit-customer.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function editCustomer(Request $request, Application $app)
    {
        $customer = new Customer();
        $jsonData = $app['services.myobAPI']->getCustomerById($request->get('id'));
        $customer->populateFromJson($jsonData["Items"][0]);
        
        $form = $app['form.factory']->create("quotemaker\\forms\\CustomerFormType", $customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $form->getData();
            $app['services.myobAPI']->addCustomer($customer);
            $this->service->insertOrUpdateCustomer($customer);
            return $app->redirect("/customerDetails/{$customer->getId()}");
        }

        return $this->twig->render('admin/edit-customer.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function newQuote2(Request $request, Application $app, $customerId, $isSiteUnseen)
    {
        $dbQuote = new Quote ();
        $dbQuote->setQuoteUUID ( uniqid () );
        $dbQuote->setCustomerId ( $customerId );
        $dbQuote->setFenceTypeId(1);
        $quoteId = $this->service->insertQuote ( $dbQuote, $this->getUsername($app), $isSiteUnseen );

        return $app->redirect ( "/showQuote/{$quoteId}" );
    }
    
    private function getUsername($app)
    {
        $token = $app['security.token_storage']->getToken();
        $user = $token->getUser();
        return $user->getUsername();
    }   
    
}
