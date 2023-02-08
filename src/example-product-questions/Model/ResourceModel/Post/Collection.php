<?php
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/
declare(strict_types=1);

namespace SwiftOtter\ProductQuestions\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SwiftOtter\ProductQuestions\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;

class Collection extends AbstractCollection
{
    const ADD_ANSWERS = 'add_answers';

    private PostCollectionFactory $postCollectionFactory;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        PostCollectionFactory $postCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->postCollectionFactory = $postCollectionFactory;
    }

    protected function _construct()
    {
        $this->_init(
            \SwiftOtter\ProductQuestions\Model\Post::class,
            \SwiftOtter\ProductQuestions\Model\ResourceModel\Post::class
        );
    }

    public function addProductIdFilter(int $productId): Collection
    {
        $this->addFieldToFilter('product_id', $productId);

        return $this;
    }

    public function addQuestionsOnlyFilter(): Collection
    {
        $this->addFieldToFilter('parent_id', ['null' => true]);

        return $this;
    }

    public function addAnswersOnlyFilter(?array $questionIds = null): Collection
    {
        if ($questionIds !== null) {
            $this->addFieldToFilter('parent_id', ['in' => $questionIds]);
        } else {
            $this->addFieldToFilter('parent_id', ['notnull' => true]);
        }

        return $this;
    }

    public function addAnswers(): Collection
    {
        $this->setFlag(self::ADD_ANSWERS, true);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        if ($this->getFlag(self::ADD_ANSWERS)) {
            $questionPosts = $this->getItems();
            $ids = array_keys($questionPosts);

            /** @var Collection $answersCollection */
            $answerPosts = $this->postCollectionFactory->create();
            $answerPosts->addAnswersOnlyFilter($ids)
                ->addOrder('updated_at', 'DESC');

            $answersByQuestionId = [];
            foreach ($answerPosts as $answer) {
                $questionId = $answer->getParentId();
                if (!$questionId) {
                    continue;
                }

                if (!isset($answersByQuestionId[$questionId])) {
                    $answersByQuestionId[$questionId] = [];
                }
                $answersByQuestionId[$questionId][] = $answer;
            }

            foreach ($questionPosts as $question) {
                $questionId = $question->getId();
                if (isset($answersByQuestionId[$questionId])) {
                    $question->setAnswers($answersByQuestionId[$questionId]);
                }
            }
        }

        return $this;
    }
}
