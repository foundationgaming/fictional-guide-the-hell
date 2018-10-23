<?php

namespace quotemaker\controllers;

use quotemaker\services\QuotemakerService;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use quotemaker\myob\MyobOauth;

class AdminController
{
    protected $twig;

    /**
     * @var QuotemakerService
     */
    protected $service;

    /**
     * @var MyobOauth
     */
    //protected $myobOauth;

    function __construct($twig, $service)
    {
        $this->twig = $twig;
        $this->service = $service;
        //$this->myobOAuth = $myobOauth;
    }

    public function colours(Request $request, Application $app)
    {
        $colours = $this->service->getAllColours();
        return $this->twig->render('admin/colours.html.twig', array('colours' => $colours));
    }

    public function newColour(Request $request, Application $app)
    {
        $form = $app['form.factory']->create("quotemaker\\forms\\ColourFormType");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $colour = $form->getData();
            $this->service->addColour($colour);
            return $app->redirect($app["url_generator"]->generate("/admin/colours"));
        }
        return $this->twig->render('admin/edit-colour.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function editColour(Request $request, Application $app)
    {
        $colour = $this->service->getColourById($request->get('id'));
        $form = $app['form.factory']->create("quotemaker\\forms\\ColourFormType", $colour);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $colour = $form->getData();
            $this->service->updateColour($colour);
            return $app->redirect($app["url_generator"]->generate("/admin/colours"));
        }

        return $this->twig->render('admin/edit-colour.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deleteColour(Request $request, Application $app)
    {
        $this->service->deleteColour($request->get('id'));
        return $app->redirect($app["url_generator"]->generate("/admin/colours"));
    }

    public function fenceTypes(Request $request, Application $app)
    {
        $fenceTypes = $this->service->getAllFenceTypes();
        return $this->twig->render('admin/fencetypes.html.twig', array('fenceTypes' => $fenceTypes));
    }

    public function newFencetype(Request $request, Application $app)
    {
        $form = $app['form.factory']->create("quotemaker\\forms\\FenceTypeFormType");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fencetype = $form->getData();
            $this->service->addFencetype($fencetype);
            return $app->redirect($app["url_generator"]->generate("/admin/fencetypes"));
        }
        return $this->twig->render('admin/edit-fencetype.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function editFenceType(Request $request, Application $app)
    {
        $fenceType = $this->service->getFenceTypeById($request->get('id'));
        $form = $app['form.factory']->create("quotemaker\\forms\\FenceTypeFormType", $fenceType);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fenceType = $form->getData();
            $this->service->updateFenceType($fenceType);
            return $app->redirect($app["url_generator"]->generate("/admin/fencetypes"));
        }

        return $this->twig->render('admin/edit-fencetype.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deleteFencetype(Request $request, Application $app)
    {
        $this->service->deleteFencetype($request->get('id'));
        return $app->redirect($app["url_generator"]->generate("/admin/fencetypes"));
    }

    public function items(Request $request, Application $app)
    {
        $items = $this->service->getAllItems();
        return $this->twig->render('admin/items.html.twig', array('items' => $items));
    }

    public function newItem(Request $request, Application $app)
    {
        $form = $app['form.factory']->create("quotemaker\\forms\\ItemFormType");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $this->service->addItem($item);
            return $app->redirect($app["url_generator"]->generate("/admin/items"));
        }
        return $this->twig->render('admin/edit-item.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function editItem(Request $request, Application $app)
    {
        $item = $this->service->getItemById($request->get('id'));
        $form = $app['form.factory']->create("quotemaker\\forms\\ItemFormType", $item);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $this->service->updateItem($item);
            return $app->redirect($app["url_generator"]->generate("/admin/items"));
        }

        return $this->twig->render('admin/edit-item.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deleteItem(Request $request, Application $app)
    {
        $this->service->deleteItem($request->get('id'));
        return $app->redirect($app["url_generator"]->generate("/admin/items"));
    }

    public function panels(Request $request, Application $app)
    {
        $panels = $this->service->getAllPanels();
        return $this->twig->render('admin/panels.html.twig', array('panels' => $panels));
    }

    public function newPanel(Request $request, Application $app)
    {
        $form = $app['form.factory']->create("quotemaker\\forms\\PanelFormType");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $panel = $form->getData();
            $this->service->addPanel($panel);
            return $app->redirect($app["url_generator"]->generate("/admin/panels"));
        }
        return $this->twig->render('admin/edit-panel.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function editPanel(Request $request, Application $app)
    {
        $panel = $this->service->getPanelById($request->get('id'));
        $form = $app['form.factory']->create("quotemaker\\forms\\PanelFormType", $panel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $panel = $form->getData();
            $this->service->updatePanel($panel);
            return $app->redirect($app["url_generator"]->generate("/admin/panels"));
        }


        return $this->twig->render('admin/edit-panel.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function deletePanel(Request $request, Application $app)
    {
        $this->service->deletePanel($request->get('id'));
        return $app->redirect($app["url_generator"]->generate("/admin/panels"));
    }

    public function editEmailSettings(Request $request, Application $app)
    {
        $bodyText = $this->service->getEmailBodyText();
        $footerText = $this->service->getEmailFooterText();
        $form = $app['form.factory']->create("quotemaker\\forms\\EmailSettingsFormType", array("bodyText" => $bodyText, "footerText" => $footerText));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->service->setEmailBodyText($data["bodyText"]);
            $this->service->setEmailFooterText($data["footerText"]);
        }

        return $this->twig->render('admin/email-settings.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function users(Request $request, Application $app)
    {
        $users = $this->service->getAllUsers();
        return $this->twig->render('admin/users.html.twig', array('users' => $users));
    }


    public function editUser(Request $request, Application $app)
    {
        $user = $this->service->getUserById($request->get('id'), array($app['security.default_encoder']));
        $form = $app['form.factory']->create("quotemaker\\forms\\UserFormType", $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->service->updateUser($user);
            return $app->redirect($app["url_generator"]->generate("/admin/users"));
        }


        return $this->twig->render('admin/edit-user.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function newUser(Request $request, Application $app)
    {
        $form = $app['form.factory']->create("quotemaker\\forms\\UserFormType");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->service->addUser($user);
        }

        return $this->twig->render('admin/edit-user.html.twig', array(
                'form' => $form->createView()
        ));
    }

    public function deleteUser(Request $request, Application $app)
    {
        $this->service->deleteUser($request->get('id'));
        return $app->redirect($app["url_generator"]->generate("/admin/users"));
    }


    public function editGatePricing(Request $request, Application $app)
    {
        $gatePricing = $this->service->getGatePricing();
        $form = $app['form.factory']->create("quotemaker\\forms\\GatePricingFormType", $gatePricing);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gatePricing = $form->getData();
            $this->service->updateGatePricing($gatePricing);
        }

        return $this->twig->render('admin/edit-gatepricing.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function loginToMYOB(Request $request, Application $app)
    {
        $this->service->deleteMyobSettings();
        /**
         * 
         * @var MyobOauth $myobOauth
         */
        $myobOauth = $app["services.myobAPI"];
        return $app->redirect("https://secure.myob.com/oauth2/account/authorize?client_id={$myobOauth->getApiKey()}&redirect_uri={$myobOauth->getRedirectUrl()}&response_type=code&scope=CompanyFile");        
    }
        
    public function connectToMYOB(Request $request, Application $app)
    {
        $code = $request->get ('code');
        $myobOauth = $app["services.myobAPI"];
        $oauthTokens = $myobOauth->getAccessToken($code);
        $this->service->updateOauthTokens($oauthTokens->access_token, $oauthTokens->refresh_token, $oauthTokens->expires_in);
        $companyFilesList = $myobOauth->getCompanyFiles();
        $this->service->updateMyobFileSetting($companyFilesList[0]->Uri);
        return $app->redirect("/");
    }

    public function chooseMyobFile(Request $request, Application $app) {
        $myobOauth = $app['services.myobAPI'];

        $resultData = $myobOauth->getCompanyFiles();
        
        $form = $app['form.factory']->create("quotemaker\\forms\\MyobFileFormType", null, array('companyFileChoices' => $resultData));
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $theData = $form->getData();
            $this->service->updateMyobFileSetting($theData['companyFile']->Uri);
        }
        
        return $this->twig->render('admin/chooseMyobFile.html.twig', array(
            'form' => $form->createView()
        ));        
    }
    
    public function showMyobCustomer(Request $request, Application $app) {
        $uri = $request->get ('id');
        $myobOauth = $app["services.myobAPI"];
        $resultData = $myobOauth->getContact($uri);
        return "";
    }

    private function getUsername($app)
    {
        $token = $app['security.token_storage']->getToken();
        $user = $token->getUser();
        return $user->getUsername();
    }

}
