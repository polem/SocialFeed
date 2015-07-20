<?php

namespace Lns\SocialFeed\Provider;

use Lns\SocialFeed\Model\Feed;
use Lns\SocialFeed\Client\ClientInterface;

use Lns\SocialFeed\Factory\PostFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lns\SocialFeed\Model\ResultSet;

class FacebookPagePostsProvider extends AbstractProvider
{
    private $client;
    private $postFactory;

    public function __construct(ClientInterface $client, PostFactoryInterface $postFactory)
    {
        $this->client = $client;
        $this->postFactory = $postFactory;
    }

    public function getResult(array $options = array()) {

        $options = $this->resolveOptions($options);

        $fieldsString = $this->generateFieldsQueryString(array(
            'actions',
            'caption',
            'created_time',
            'id',
            'likes',
            'link',
            'message',
            'message_tags',
            'full_picture',
            'shares',
            'type',
            'from' => array(
                'name',
                'id',
                'picture.type(large)',
                'link'
            )
        ));

        $feed = new Feed();

        $data = $this
            ->client
            ->get('/' . $options['page_id'] . '/posts?fields=' . $fieldsString);

        foreach($data['data'] as $postData) {
            $feed->addPost($this->postFactory->create($postData));
        }

        $nextResultOptions = array();

        // extract pagination parameters
        if(isset($data['paging']['next'])) {
            $nextResultOptions['next'] = $this->extractUrlParameters($data['paging']['next']);
        }

        return new ResultSet($feed, $nextResultOptions);
    }

    public function getName()
    {
        return 'facebook_page';
    }

    protected function configureOptionResolver(OptionsResolver &$resolver) {
        $resolver->setRequired('page_id');
    }

    private function generateFieldsQueryString($fields) {
        $parts = array();

        foreach($fields as $fieldKey => $fieldValue) {
            $parts[] = is_array($fieldValue) ? $fieldKey . '.fields(' . $this->generateFieldsQueryString($fieldValue) . ')' : $fieldValue;
        }

        return join($parts, ',');
    }
}
