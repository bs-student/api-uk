<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the password change
 *
 */
class DBManagementController extends Controller
{

    /**
     * Get Old Campuses and put to new one
     */
    public function addCampusesFromOldToNewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare(
            'INSERT INTO `campuses`(`id`,`university_id`, `state_id`, `campus_name`)
            SELECT
            gl_subsystem.subsystemID,
            gl_subsystem.topsystemIDFK,
            states.id as state_id,
            gl_subsystem.subsystem_name
            FROM gl_subsystem
            LEFT JOIN states ON gl_subsystem.state = states.state_short_name'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();

//        $result = $statement->fetchAll(); // note: !== $connection->fetchAll()!


    }


    /**
     * Get Old Books and put to new one
     */
    public function addBooksFromOldToNewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare(
            'INSERT INTO `books`(`id`,`book_title`, `book_director_author_artist`,
			`book_edition`,`book_isbn10`,`book_isbn13`,`book_publisher`,
			`book_publish_date`,`book_binding`,`book_page`,`book_language`,
			`book_image`,`book_amazon_price`)
            SELECT
            gl_book.bookID,
            gl_book.book_title,
            gl_book.book_director_author_artist,
			gl_book.book_edition,
			gl_book.book_isbn_10,
			gl_book.book_isbn_13,
			gl_book.book_publisher,
			gl_book.book_publisher_date,
			gl_book.book_binding,
			gl_book.book_page,
			gl_book.book_language,
			gl_book.book_image_large_url,
			gl_book.book_price_amazon_new

            FROM gl_book'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();

//        $result = $statement->fetchAll(); // note: !== $connection->fetchAll()!


    }


    /**
     * Get Old Users and put to new one
     */
    public function addUsersFromOldToNewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'INSERT INTO `users`(`id`,`campus_id`, `username`,`username_canonical`, `email`,`email_canonical`,`enabled`,`salt`,`password`,`locked`,`expired`,
			`roles`,`credentials_expired`,`full_name`,`registration_status`,`admin_approved`)
            SELECT
            gl_info_members.memberID,
            gl_info_members.subsystemIDFK,
            gl_info_members.screenname,
			LOWER(gl_info_members.screenname),
			gl_info_members.email,
			LOWER(gl_info_members.email),
			gl_info_members.activ,
			gl_info_members.password_salt,
			gl_info_members.password,
			0,
			0,
			(case when (gl_info_members.adminfunction = "Admin")
                 THEN
                      \'a:2:{i:0;s:16:"ROLE_NORMAL_USER";i:1;s:15:"ROLE_ADMIN_USER";}\'
                 ELSE
                      \'a:1:{i:0;s:16:"ROLE_NORMAL_USER";}\'
                 END),
            0,
            gl_info_members.fullname,
            "complete",
            (case when (gl_info_members.approved = 1)
                 THEN
                      "Yes"
                 ELSE
                      "No"
                 END)

            FROM gl_info_members'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();

//        $result = $statement->fetchAll(); // note: !== $connection->fetchAll()!


    }

    public function addBookDealsFromOldToNewAction(){
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'INSERT INTO `book_deals`(`id`,`book_id`, `seller_id`,`book_price_sell`,
			`book_condition`,`book_is_highlighted`,`book_has_notes`,
			`book_comment`,`book_contact_method`,`book_contact_home_number`,`book_contact_cell_number`,
			`book_contact_email`,`book_is_available_public`,`book_payment_method_cash_on_exchange`,
			`book_payment_method_cheque`,`book_available_date`,`book_selling_status`,`book_view_count`,
			`book_status`,`book_submitted_date_time`)
            SELECT
            gl_sell_book.book_sellID,
            gl_sell_book.bookIDFK,
            gl_sell_book.memberIDFK,
			gl_sell_book.price,

			(CASE
                WHEN gl_sell_book.book_condition = "barely_used" THEN "Barely Used"
                WHEN gl_sell_book.book_condition = "heavy_used" THEN "Heavily Used"
                WHEN gl_sell_book.book_condition = "new" THEN "New"
                WHEN gl_sell_book.book_condition = "used" THEN "Used"
                ELSE 1
            END),

			(case
			    when (gl_sell_book.book_highlight = 0)
                THEN
                  "No"
                ELSE
                  "Yes"
              END),
             (case when (gl_sell_book.book_notes = 0)
             THEN
                  "No"
             ELSE
                  "Yes"
             END),

			gl_sell_book.comments,

			(CASE
                WHEN gl_sell_book.cnt_method = "buyer_to_seller" THEN "buyerToSeller"
                WHEN gl_sell_book.cnt_method = "seller_to_buyer" THEN "sellerToBuyer"
                ELSE 1
            END),

			gl_sell_book.cnt_home_phone,
			gl_sell_book.cnt_cell_phone,
			gl_sell_book.cnt_email,

			(case when (gl_sell_book.available_public = 0)
                 THEN
                      "No"
                 ELSE
                      "Yes"
                 END),

			gl_sell_book.terms_method_cash,
			gl_sell_book.terms_method_check,
			gl_sell_book.available_date,

			"Selling",

			gl_sell_book.visit,
			(case when (gl_sell_book.active = 0)
                 THEN
                      "Deactivated"
                 ELSE
                      "Activated"
                 END),
			gl_sell_book.cleanup_notification_last_contact_deal

            FROM gl_sell_book'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();
    }

    public function addContactsFromOldToNewAction(){
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'INSERT INTO `contact`(`id`,`book_deal_id`, `buyer_id`,
			`buyer_email`,`buyer_home_phone`,`buyer_cell_phone`,
			`contact_datetime`,`sold_to_that_buyer`)
            SELECT
            gl_sell_contact.sell_contactID,
            gl_sell_contact.book_sellIDFK,
			(case when (gl_sell_contact.contact_memberIDFK = 0)
                 THEN
                      NULL
                 ELSE
                      gl_sell_contact.contact_memberIDFK
                 END),

			gl_sell_contact.cnt_email,
			gl_sell_contact.cnt_home_phone,
			gl_sell_contact.cnt_cell_phone,
			gl_sell_contact.contact_date,
			"No"

            FROM gl_sell_contact'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();
    }

    public function addMessagesFromOldToNewAction(){
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare(
            'INSERT INTO `messages`(`user_id`, `contact_id`,
			`message_body`,`message_type`,`message_datetime`)
            SELECT

			(case when (gl_sell_contact.contact_memberIDFK = 0)
                 THEN
                      NULL
                 ELSE
                      gl_sell_contact.contact_memberIDFK
                 END),

            gl_sell_contact.sell_contactID,
            gl_sell_contact.cnt_message,
			"BuyerToSellerMessage",
			gl_sell_contact.contact_date

            FROM gl_sell_contact'
        );
        $statement->execute();
        $affected_rows = $statement->rowCount();
        echo $affected_rows . " rows have been affected";
        die();
    }

}
