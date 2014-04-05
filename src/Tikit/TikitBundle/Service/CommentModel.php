<?php
// Service Code
namespace Tikit\TikitBundle\Service;

use Doctrine\ORM\Mapping as ORM;
use Tikit\TikitBundle\Entity\Comment;
use Tikit\TikitBundle\Entity\TikitScore;

class CommentModel
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Gets Symfony-Barcelona info from Sensio Connect. Info is stored in APC during an hour to increase speed
     * @return array
     */
    public function addComment($tikit_id, $commentBody, $user_id, $parent_id = 0)
    {
        $comment = new Comment();
        $tikit = $this->em->find('\Tikit\TikitBundle\Entity\Tikit', $tikit_id);
        $comment->setTikit($tikit);
        $user = $this->em->find('\Tikit\TikitBundle\Entity\FosUser', $user_id);
        $comment->setUser($user);
        $comment->setParentId($parent_id);
        $comment->setCommentBody($commentBody);
        $tikit->setCommentCount($tikit->getCommentCount()+1);
        $this->em->persist($comment);
        $this->em->persist($tikit);
        $this->em->flush();
    }

    public function hideComment($comment_id)
    {
        $comment = $this->em->getRepository('Tikit\TikitBundle\Entity\Comment')->findOneBy(array('id' => $comment_id));
        $comment->setStatus(Comment::STATUS_UNPUBLISHED);
        $tikit = $comment->getTikit();
        $tikit->setCommentCount($tikit->getCommentCount()-1);
        $this->em->persist($comment);
        $this->em->persist($tikit);
        $this->em->flush();
    }


    public function markCommentAsSpam($comment_id,$user_id)
    {
        $sitespam = new SiteSpam();
        $comment = $this->em->find('\Tikit\TikitBundle\Entity\Comment', $comment_id);
        $sitespam->setCommentId($comment->getId());
        $user = $this->em->find('\Tikit\TikitBundle\Entity\FosUser', $user_id);
        $sitespam->setUser($user);
        $this->em->persist($sitespam);
        $this->em->flush();
        return 1;
    }

    public function processCommentAsSpam($comment_id,$user_id)
    {
        $sitespam = $this->em->getRepository('\Tikit\TikitBundle\Entity\SiteSpam')->findOneBy(array('commentId' => $comment_id));
        $sitespam->setStatus(SiteSpam::PROCESSED);
        if(!$spamusercount = $this->em->getRepository('\Tikit\TikitBundle\Entity\SpamUserCount')->findOneBy(array('user' => $user_id)))
        {
            $spamusercount = new SpamUserCount();
            $spamusercount->setUser($user);
        } else {
            $spamusercount->setSpamNumber($spamusercount->getSpamNumber()+1);
        }
        $this->em->persist($spamusercount);
        $comment = $this->em->getRepository('\Tikit\TikitBundle\Entity\Comment')->findOneBy(array('id' => $comment_id));
        $comment->setStatus(Comment::STATUS_UNPUBLISHED);
        $tikit = $comment->getTikit();
        $tikit->setCommentCount($tikit->getCommentCount()-1);
        $this->em->persist($comment);
        $this->em->persist($tikit);
        $this->em->persist($sitespam);
        $this->em->flush();
        return 1;
    }

    public function processCommentAsNotSpam($comment_id,$user_id)
    {
        $sitespam = $this->em->getPartialReference('\Tikit\TikitBundle\Entity\SiteSpam',array('commentId' => $comment_id));
        $this->em->remove($sitespam);
        $this->em->flush();
        $query = $this->em->createQuery('UPDATE \Tikit\TikitBundle\Entity\SpamUserCount s SET s.spamNumber = s.spamNumber - 1
            WHERE s.user = :user');
        $query->setParameters(array(
            'user' => $user_id
        ));
        $query->getResult();
        return 1;
    }

    public function getTikitComments($tikit_id)
    {
        $query = $this->em->createQuery('SELECT c, u.id, u.username FROM \Tikit\TikitBundle\Entity\Comment c
                                    JOIN \Tikit\TikitBundle\Entity\FosUser u WITH u.id = c.user AND c.status = :status
                                    AND c.tikit = :tikit_id');
        $query->setParameters(array(
            'status' => Comment::STATUS_PUBLISHED,
            'tikit_id' => $tikit_id,
        ));
        $comments = $query->getResult();
        return $comments;
    }
}