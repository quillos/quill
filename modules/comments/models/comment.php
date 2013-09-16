<?php

class Comment extends Model
{
    public static $table = 'comments';
    public static function getByPostId($pid = 0)
    {
        return Comment::where('post_id','=',$pid)
            ->where('approved','=',1)
            ->sort('date','DESC')
            ->get();
    }
    public static function getByParentId($parent = 0)
    {
        return Comment::where('parent','=',$parent)
            ->where('approved','=',1)
            ->sort('date','DESC')
            ->get();
    }
    public function author()
    {
        return array(
            'name' => $this->author_name,
            'url' => $this->author_url,
            'avatar' => $this->author_avatar(),
        );
    }
    public function html()
    {
        return Filter::apply('comment_content', $this->content);
    }
    public function author_avatar()
    {
        $avatar = avatar($this->author_email, 50, 'mm', 'g', false);
        $avatar = Filter::apply('author_avatar', $avatar);
        return '<img src="' . $avatar . '" class="comment-author-avatar"/>';
    }
}