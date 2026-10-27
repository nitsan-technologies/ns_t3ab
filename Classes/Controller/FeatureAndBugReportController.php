<?php

declare(strict_types=1);

namespace NITSAN\NsT3Ab\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FeatureAndBugReportController extends AbstractController
{
    public function featureSuggestionAction(ServerRequestInterface $request): ResponseInterface
    {
        $assign = [];
        return $this->getViewAndTemplate($request, $assign, 'FeatureAndBugReport/', 'FeatureSuggestion');
    }

    public function bugReportAction(ServerRequestInterface $request): ResponseInterface
    {
        $assign = [];
        return $this->getViewAndTemplate($request, $assign, 'FeatureAndBugReport/', 'BugReport');
    }

    public function submitFeatureBugAction(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $success = false;
        $type = $error = '';
        $data = [
            'submitType' => $parsedBody['submitType'] ?? '',
            'name' => $parsedBody['name'] ?? '',
            'email' => $parsedBody['email'] ?? '',
            'description' => $parsedBody['description'] ?? '',
            'issueType' => $parsedBody['issueType'] ?? '',
            'attachments' => $_FILES['attachments'] ?? null,
        ];
        if (
            $data['submitType'] !== '' &&
            $data['name'] !== '' &&
            $data['email'] !== '' &&
            $data['description'] !== ''
        ) {
            $subject = 'New Feature Request';
            if ($data['submitType'] === 'bugReport') {
                $subject = 'New Issue Reported';
            }
            $type = $data['submitType'];
            try {
                $email = new FluidEmail();
                $email
                    ->to(new Address('info@t3planet.com', 'T3AB'), new Address('editor@t3planet.com', 'T3AB'))
                    ->from(new Address('t3ai@support.com', $data['name']))
                    ->subject($subject)
                    ->format(FluidEmail::FORMAT_HTML)
                    ->setTemplate('FeatureBug')
                    ->assignMultiple($data);
                if ($data['attachments'] && is_uploaded_file($data['attachments']['tmp_name'])) {
                    $email->attachFromPath($data['attachments']['tmp_name'], $data['attachments']['name']);
                }
                GeneralUtility::makeInstance(MailerInterface::class)->send($email);
                $success = true;
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }
        return new JsonResponse([
            'success' => $success,
            'type' => $type,
            'error' => $error,
        ]);
    }
}
