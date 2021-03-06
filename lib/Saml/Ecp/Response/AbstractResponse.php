<?php

namespace Saml\Ecp\Response;

use Saml\Ecp\Soap\Message\Message;
use Saml\Ecp\Util\Options;
use Zend\Http;


/**
 * Abstract response class.
 * 
 * @copyright (c) 2013 Ivan Novakov (http://novakov.cz/)
 * @license http://debug.cz/license/freebsd
 */
abstract class AbstractResponse implements ResponseInterface
{

    /**
     * HTTP response.
     * 
     * @var Http\Response
     */
    protected $_httpResponse = null;

    /**
     * The SOAP message contained in the response.
     * 
     * @var Message
     */
    protected $_soapMessage = null;

    /**
     * Options.
     *
     * @var Options
     */
    protected $_options = null;


    /**
     * Constructor.
     *
     * @param array|\Traversable $options
     */
    public function __construct(Http\Response $httpResponse, $options = array())
    {
        $this->setOptions($options);
        $this->setHttpResponse($httpResponse);
    }


    /**
     * Sets the options.
     * 
     * @param array|\Traversable $options
     */
    public function setOptions($options)
    {
        $this->_options = new Options($options);
    }


    /**
     * {@inheritdoc}
     * @see \Saml\Ecp\Response\ResponseInterface::setHttpResponse()
     */
    public function setHttpResponse(Http\Response $httpResponse)
    {
        $this->_httpResponse = $httpResponse;
    }


    /**
     * Returns the HTTP response.
     * 
     * @return Http\Response
     */
    public function getHttpResponse()
    {
        return $this->_httpResponse;
    }


    /**
     * {@inheritdoc}
     * @see \Saml\Ecp\Response\ResponseInterface::getContent()
     */
    public function getContent()
    {
        return $this->getHttpResponse()
            ->getBody();
    }
    
    /*
    public function validate (array $validateOptions = array())
    {}
    */
    
    /**
     * {@inheritdoc}
     * @see \Saml\Ecp\Soap\Container\ContainerInterface::getSoapMessage()
     */
    public function getSoapMessage()
    {
        if (! ($this->_soapMessage instanceof Message)) {
            $this->_soapMessage = $this->_createSoapMessage($this->getContent());
        }
        
        return $this->_soapMessage;
    }


    /**
     * {@inheritdoc}
     * @see \Saml\Ecp\Soap\Container\ContainerInterface::setSoapMessage()
     */
    public function setSoapMessage(Message $soapMessage)
    {
        $this->_soapMessage = $soapMessage;
    }


    /**
     * FIXME - move to separate object - ResponseSerializer
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s: [%d]", $this->_getResponseName(), $this->getHttpResponse()
            ->getStatusCode());
    }


    /**
     * Returns a "readable" response class name.
     * 
     * @return string
     */
    protected function _getResponseName()
    {
        $className = get_class($this);
        return substr($className, strrpos($className, '\\') + 1);
    }


    /**
     * Creates and returns a SOAP message object with the provided content.
     * 
     * @param string $content
     * @return Message
     */
    protected function _createSoapMessage($content)
    {
        return new Message($content);
    }
}